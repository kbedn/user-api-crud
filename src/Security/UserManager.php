<?php namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\{
    EntityManagerInterface,
    OptimisticLockException,
    ORMException
};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    /** @var UserPasswordEncoderInterface $encoder */
    protected $encoder;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var UserProvider */
    protected $userProvider;

    /**
     * UserManager constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @param UserProvider $provider
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager,
        UserProvider $provider
    ) {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
        $this->userProvider = $provider;
    }

    /**
     * @param array $userData
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createUserFromArray(array $userData): User
    {
        if (!$userData['password'] || !$userData['email']){
            throw new \InvalidArgumentException('Not enough data');
        }

        $user = new User();
        $user->setEmail($userData['email']);
        $this->updatePassword($user, $userData['password']);

        $this->presistUser($user);

        return $user;
    }
    /**
     * @param UserInterface $user
     */
    protected function presistUser(UserInterface $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param string $field
     * @param string $key
     * @return User|null
     */
    public function findUser(string $field, string $key = 'username'): ?User
    {
        return $this->userProvider->userRepository->findOneBy([$key => $field]);
    }

    /**
     * @param UserInterface $user
     * @param string $pass
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updatePassword(UserInterface $user, string $pass): void
    {
        $this->userProvider->userRepository->upgradePassword($user, $this->encoder->encodePassword($user, $pass));
    }
}
