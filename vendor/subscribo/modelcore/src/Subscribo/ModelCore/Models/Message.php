<?php
namespace Subscribo\ModelCore\Models;


/**
 * Model Message
 *
 * Model class for being changed and used in the application
 *
 * @property array $messageData JSON encoded array - careful - can be saved only whole
 */
class Message extends \Subscribo\ModelCore\Bases\Message
{
    /**
     * @param Account $account
     * @param array $messageData
     * @param string|null $content
     * @param string|bool $contentType
     * @param string|null $subject
     * @param $status
     * @return Message
     */
    public static function generateEmailForAccount(
        Account $account,
        array $messageData = [],
        $content = null,
        $contentType = true,
        $subject = null,
        $status = self::STATUS_PLANNED
    ) {
        $instance = static::makeEmailForAccount($account, $messageData, $content, $contentType, $subject, $status);
        $instance->save();

        return $instance;
    }

    /**
     * @param Account $account
     * @param array $messageData
     * @param string|null $content
     * @param string|bool $contentType
     * @param string|null $subject
     * @param string $status
     * @return Message
     */
    public static function makeEmailForAccount(
        Account $account,
        array $messageData = [],
        $content = null,
        $contentType = true,
        $subject = null,
        $status = self::STATUS_PLANNED
    ) {
        $instance = new self();
        $instance->account()->associate($account);
        $instance->serviceId = $account->serviceId;
        $instance->type = static::TYPE_EMAIL;
        $instance->status = $status;
        if ($contentType === true) {
            $contentType = static::CONTENT_TYPE_TEXT;
        }
        if (isset($content)) {
            $instance->content = $content;
            $instance->contentType = $contentType;
        }
        $instance->messageData = $messageData;
        $instance->synchroniseSubject($subject);

        return $instance;
    }

    /**
     * @param null|string $subject
     */
    public function synchroniseSubject($subject = null)
    {
        $data = $this->messageData ?: [];
        if (empty($subject)) {
            $subject = $this->subject;
        }
        if (empty($subject) and isset($data['subject'])) {
            $subject = $data['subject'];
        }
        $this->subject = $subject;
        $data['subject'] = $subject;
        $this->messageData = $data;
    }

    public function addEmailToFromAccount()
    {
        if (empty($this->account->customer->email)) {

            return;
        }
        $toEmail = $this->account->customer->email;
        $toName = empty($this->account->customer->person->name) ? null : $this->account->customer->person->name;
        $data = $this->messageData ?: [];
        $to = empty($data['to']) ? [] : $data['to'];
        $toSource= is_array($to) ? $to : [$to];
        $toModified = [];
        $alreadyPresent = false;
        foreach ($toSource as $key => $value) {
            if (is_int($key)) {
                if ($value === $toEmail) {
                    //We will skip this found occurrence for now and add it in the end (if not found elsewhere)
                    continue;
                }
                $toModified[] = $value;
            } else {
                if ($key === $toEmail) {
                    $value = $toName ?: $value;
                    $alreadyPresent = true;
                }
                $toModified[$key] = $value;
            }
        }
        if ( ! $alreadyPresent) {
            $toModified[$toEmail] = $toName;
        }
        $data['to'] = $toModified;
        $this->messageData = $data;
    }

    public function getMessageDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setMessageDataAttribute($value)
    {
        $this->attributes['message_data'] = json_encode($value, JSON_BIGINT_AS_STRING);
    }

}
