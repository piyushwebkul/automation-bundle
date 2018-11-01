<?php

namespace Webkul\UVDesk\AutomationBundle\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture as DoctrineFixture;
use Webkul\UVDesk\AutomationBundle\Entity as AutomationEntities;

class AutomationWorkflowFixtures extends DoctrineFixture
{
    private static $seeds = [
        [
            'name' => 'Customer Account Created',
            'description' => 'Send customer a welcome email after their account has been created.',
            'conditions' => 'N;',
            'actions' => 'a:1:{i:1;a:2:{s:4:"type";s:29:"uvdesk.customer.mail_customer";s:5:"value";s:1:"8";}}',
            'status' => '1',
            'sort_order' => '1',
            'events' => ['uvdesk.customer.created']
        ],
        [
            'name' => 'Agent Account Created',
            'description' => 'Send agent a welcome email when their account is created.',
            'conditions' => 'N;',
            'actions' => 'a:1:{i:1;a:2:{s:4:"type";s:23:"uvdesk.agent.mail_agent";s:5:"value";s:1:"3";}}',
            'status' => '1',
            'sort_order' => '2',
            'events' => ['uvdesk.agent.created']
        ],
    ];

    public function load(ObjectManager $entityManager)
    {
        $availableWorkflows = $entityManager->getRepository('UVDeskAutomationBundle:Workflow')->findAll();

        if (empty($availableWorkflows)) {
            foreach (self::$seeds as $baseEvent) {
                $workflow_actions = unserialize($baseEvent['actions']);
                
                ($workflow = new AutomationEntities\Workflow())
                    ->setConditions([])
                    ->setDateAdded(new \DateTime)
                    ->setDateUpdated(new \DateTime)
                    ->setIsPredefind(true)
                    ->setActions($workflow_actions)
                    ->setName($baseEvent['name'])
                    ->setStatus($baseEvent['status'])
                    ->setSortOrder($baseEvent['sort_order'])
                    ->setDescription($baseEvent['description']);
                
                $entityManager->persist($workflow);
                $entityManager->flush();
    
                foreach ($baseEvent['events'] as $eventValue) {
                    $eventObj = new AutomationEntities\WorkflowEvents();
                    $eventObj->setEventId($workflow->getId());
                    $eventObj->setEvent($eventValue);
                    $eventObj->setWorkflow($workflow);
                    $entityManager->persist($eventObj);
                }
                $entityManager->flush();
            }
        }
    }
}