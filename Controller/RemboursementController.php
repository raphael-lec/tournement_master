<?php

class RemboursementController extends AbstractController
{
    public function showRemboursement(string $codeGroupe): void
    {
        $groupeManager = new GroupeManager();
        $balanceManager = new BalanceManager();

        $group = $groupeManager->getGroupBycode($codeGroupe);

        if ($group === null) {
            (new PageController())->notFound();
            return;
        }

        $groupeId = $group->getId();
        
        $balances = $balanceManager->calculateGroupBalances($groupeId);

        $members = $groupeManager->getGroupParticipants($groupeId);
        $memberNames = [];
        foreach ($members as $member) {
            $memberNames[$member->getId()] = $member->getUsername();
        }

        $data = [
            'groupe' => $group,
            'code_groupe' => $codeGroupe,
            'balances' => $balances,
            'memberNames' => $memberNames
        ];
        
        (new PageController())->render('remboursement', $data);
    }
    
}