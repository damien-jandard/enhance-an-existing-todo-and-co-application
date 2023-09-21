<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;
use App\Security\Voter\TaskVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoterTest extends TestCase
{
    public function testAccessDeniedWhenNotUserInstance(): void
    {
        $voter = new TaskVoter();
        $subject = new Task();
        $token = $this->createMock(TokenInterface::class);
        $result = $voter->vote($token, $subject, [TaskVoter::CAN_UPDATE]);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }
}
