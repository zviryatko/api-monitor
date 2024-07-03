<?php

namespace App\Service;

use App\Entity\Job;
use Laminas\Http\Client;

class JobExecute
{
    /**
     * Execute the job.
     *
     * @param Job $job
     * @return bool
     */
    public function execute(Job $job): bool
    {
        try {
            $code = (new Client(
                $job->getUrl(),
                [
                    'maxredirects' => 0,
                    'timeout' => 3,
                ],
            ))->send()->getStatusCode();
        } catch (\Exception $e) {
            return false;
        }
        return $code >= 200 && $code < 300;
    }
}
