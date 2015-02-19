<?php namespace Subscribo\App\Model;


/**
 * Model Person
 *
 * Model class for being changed and used in the application
 */
class Person extends \Subscribo\App\Model\Base\Person
{
    protected $appends = ['name'];

    /**
     * @see http://de.wikipedia.org/wiki/Namenszusatz#Personen
     */
    protected static $suffixes = ['PhD.', 'MdB', 'MdL',  'MdEP', 'MdA', 'MdHB', 'StB', 'WB', 'RA', 'FA',
        'B.Sc.', 'B.A.', 'B.Ed.', 'B.Eng.', 'M.Sc.', 'M.A.', 'M.Ed.', 'M.Eng.', 'MBA', 'LL.M.',
        'senior', 'sen.', 'sr.', 'snr.', 'junior', 'jun.', 'jr.', 'jnr.', ];

    protected static $infixes = ['von', 'of', 'from', 'de', 'te', 'van', 'di', 'del', 'dello', 'della', 'dei', 'delle', 'da', 'dal', 'z', 'ze'];

    protected static $prefixes = ['Herr', 'Frau', 'Dr.', 'Dipl.-Ing.', 'Ing.', 'Bc.', 'Mag.', 'LAbg.', 'HR', 'Abg.z.NR',  'Prof.', 'Univ.Prof.', 'doc.', 'Mgr.', 'et.' ];

    public static function generate($name, $gender = null)
    {
        if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
            $name = self::deriveNameFromEmail($name);
        }
        $instance = new self();
        $instance->gender = $gender;
        $instance->assignName($name);
        $instance->save();
        return $instance;
    }

    protected function assignName($name)
    {
        $this->clearName();
        $name = trim($name);
        $normalized = preg_replace('/ +/', ' ', $name);
        /** Suffix handling */
        $commaSeparated = explode(',', $normalized, 2);
        $normalized = trim(array_shift($commaSeparated));
        $parts = explode(' ', $normalized);
        if ($commaSeparated) {
            $this->suffix = reset($commaSeparated) ?: null;
        } elseif (count($parts) > 2) {
            $last = end($parts);
            foreach (self::$suffixes as $suffix) {
                if ($last === $suffix) {
                    $this->suffix = array_pop($parts);
                    break;
                }
            }
        }
        /** Last Name */
        $this->lastName = array_pop($parts);
        /** Infix handling */
        $last = end($parts);
        foreach (self::$infixes as $infix) {
            if ($last === $infix) {
                $this->infix = array_pop($parts);
            }
        }
        /** Prefix Handling */
        while ($parts) {
            $first = reset($parts);
            $found = array_search($first, self::$prefixes, true);
            if (false === $found) {
                break;
            }
            $prefix = $this->prefix.' '.array_shift($parts);
            $this->prefix = trim($prefix);
        }
        /** First and middle names */
        $this->firstName = array_shift($parts);
        if ($parts) {
            $this->middleNames = implode(' ', $parts);
        }
    }

    protected function clearName()
    {
        $this->prefix = null;
        $this->firstName = null;
        $this->middleNames = null;
        $this->infix = null;
        $this->lastName = null;
        $this->suffix = null;
    }

    private static function deriveNameFromEmail($email)
    {
        $username = strstr($email, '@', true);
        $normalized = preg_replace('/[^a-zA-Z]+/', ' ', $username);
        $parts = explode(' ', $normalized);
        $result = '';
        foreach ($parts as $part) {
            if (empty($part)) {
                continue;
            }
            $result .= ' '.ucfirst($part);
        }
        $result = trim($result);
        return $result;
    }

    public function getNameAttribute()
    {
        $result = $this->prefix.' '.$this->firstName.' '.$this->middleNames.' '.$this->infix.' '.$this->lastName.', '.$this->suffix;
        $result = trim($result, ', ');
        $result = preg_replace('/ +/', ' ', $result);
        return $result;
    }


}
