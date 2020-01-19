<?php namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\{Exception\UnsupportedUserException,
    Exception\UsernameNotFoundException,
    User\UserInterface,
    User\UserProviderInterface
};

class UserProvider implements UserProviderInterface
{
    /** @var UserRepository */
    public $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->findUser($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
                sprintf('Expected an instance of %s, but got "%s".', User::class, get_class($user))
            );
        }

        if (null === $reloadedUser = $this->userRepository->findOneBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }

    /**
     * @param string $username
     * @return User|null
     */
    protected function findUser($username): ?User
    {
        return $this->userRepository->loadUserByUsername($username);
    }
}
