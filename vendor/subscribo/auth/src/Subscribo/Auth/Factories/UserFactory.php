<?php namespace Subscribo\Auth\Factories;

use Subscribo\Auth\Interfaces\StatelessAuthenticatableFactoryInterface;
use Subscribo\RestCommon\Interfaces\ByTokenIdentifiableFactoryInterface;
use Subscribo\RestCommon\Interfaces\CommonSecretProviderInterface;
use Subscribo\RestCommon\Interfaces\TokenRingProviderInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Subscribo\App\Model\User;
use Subscribo\App\Model\UserToken;
use Subscribo\Auth\Exceptions\InvalidArgumentException;

class UserFactory implements StatelessAuthenticatableFactoryInterface, ByTokenIdentifiableFactoryInterface
{

    /**
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * @var CommonSecretProviderInterface
     */
    protected $commonSecretProvider;

    public function __construct(Hasher $hasher, CommonSecretProviderInterface $commonSecretProvider)
    {
        $this->hasher = $hasher;
        $this->commonSecretProvider = $commonSecretProvider;
    }

    /**
     * @param array $data
     * @return User
     */
    public function create(array $data = array())
    {
        if (array_key_exists('password', $data)) {
            $hashedPassword = $this->hasher->make($data['password']);
            $data['password'] = $hashedPassword;
        }
        $user = new User($data);
        return $user;
    }

    public function setUserPassword(User $user, $newPassword)
    {
        $user->password = $this->hasher->make($newPassword);
    }

    /**
     * Create token(s) of specified type(s) for given user. If user does not yet have id, saves it.
     *
     * @param User|int $user User object or user Id
     * @param array|string|bool $tokenTypes (true for preselected set)
     * @return array
     * @throws InvalidArgumentException
     */
    public function addTokens($user, $tokenTypes = true)
    {
        if (true === $tokenTypes) {
            $tokenTypes = [UserToken::TYPE_SUBSCRIBO_DIGEST, UserToken::TYPE_SUBSCRIBO_BASIC];
        }
        if ( ! is_array($tokenTypes)) {
            $tokenTypes = [$tokenTypes];
        }
        if (empty($user)) {
            throw new InvalidArgumentException('UserFactory::addTokens() user parameter should contain user object or user ID');
        }
        if ($user instanceof User and ( ! $user->id)) {
            $user->save();
        }
        $commonSecret = ($this->commonSecretProvider) ? $this->commonSecretProvider->getCommonSecret() : null;
        $result = [];
        foreach ($tokenTypes as $tokenType) {
            $result[] = UserToken::generateTokenForUser($user, $tokenType, $commonSecret);
        }
        return $result;
    }


    /**
     * @param mixed $id
     * @return User|Authenticatable
     * @throws InvalidArgumentException
     */
    public function retrieveById($id)
    {
        if ( ! is_numeric($id)) {
            throw new InvalidArgumentException('Id of an user should be numeric.');
        }
        $id = intval($id);
        $user = User::find($id);
        return $user;
    }

    /**
     * @param array $credentials
     * @return User|Authenticatable
     * @throws InvalidArgumentException
     */
    public function retrieveByCredentials(array $credentials)
    {
        $email = isset($credentials['email']) ? $credentials['email'] : null;
        $username = isset($credentials['username']) ? $credentials['username'] : null;
        if (empty($email) and empty($username)) {
            throw new InvalidArgumentException('Both username and email are empty');
        }
        $query = User::query();
        if ($email) {
            $query->where('email', $email);
        } else {
            $query->where('username', $username);
        }
        $user = $query->first();
        return $user;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (empty($credentials['password'])) {
            throw new InvalidArgumentException('Credentials does not contain password or password is empty');
        }
        $result = $this->validatePassword($user, $credentials['password']);
        return $result;

    }

    public function validatePassword(Authenticatable $user, $passwordToValidate)
    {
        $result = $this->hasher->check($passwordToValidate, $user->getAuthPassword());
        return $result;
    }


    /**
     * @param TokenRingProviderInterface $tokenRingProvider
     * @return null|\Subscribo\RestCommon\Interfaces\ByTokenIdentifiableInterface
     */
    public function findByTokenIdentifiableUsingTokenRingProvider(TokenRingProviderInterface $tokenRingProvider)
    {
        $user = $tokenRingProvider->provideByTokenIdentifiable();
        return $user;
    }

    /**
     * @param string $token
     * @param string|null $tokenType
     * @return UserToken|null|TokenRingProviderInterface
     */
    public function tokenToTokenRingProvider($token, $tokenType = null)
    {
        $userToken = UserToken::findByTokenAndType($token, $tokenType);
        return $userToken;
    }
}
