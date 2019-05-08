<?php


namespace App\Service;


use App\Entity\Invitation;

class InvitationManager
{

    /**
     * @param Invitation $invitation
     * Send invitation to designated email
     */
    public function send(Invitation $invitation){

    }

    /**
     * @param string $invitationId
     * @return Invitation|null
     */
    public function findInvitation(string $invitationId): ?Invitation{

    }

    /**
     * @param Invitation $invitation
     */
    public function discardInvitation(Invitation $invitation): void{

    }

}