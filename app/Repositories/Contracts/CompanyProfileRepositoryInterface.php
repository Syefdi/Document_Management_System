<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface CompanyProfileRepositoryInterface extends BaseRepositoryInterface
{
    public function getCompanyProfile();
    public function updateCompanyProfile($attribute);
    public function updateStorage($attribute);
    public function getStorage();
    public function saveOpenAiKey($request);
    public function geteOpenAiKey();
<<<<<<< HEAD
    public function updateLicense($attribute);
=======
>>>>>>> 6895172fd2f31385a5c656d4e4aa7daeb185abfc
}
