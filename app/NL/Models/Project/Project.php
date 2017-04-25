<?php

namespace app\NL\Models\Project;

use app\NL\Models\Company\Company;

class Project extends Company implements IProject
{
    private $project_name;
    private $project_status;

    /**
     * Project constructor.
     * @param $company_name
     * @param $address
     * @param $business_area
     * @param $project_name
     * @param $project_status
     */
    public function __construct($project_name, $project_status, $company_name, $address, $business_area)
    {
        parent::__construct($company_name, $address, $business_area);
        $this->project_name = $project_name;
        $this->project_status = $project_status;
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->project_name;
    }

    /**
     * @param mixed $project_name
     */
    public function setProjectName($project_name)
    {
        $this->project_name = $project_name;
    }

    /**
     * @return mixed
     */
    public function getProjectStatus()
    {
        return $this->project_status;
    }

    /**
     * @param mixed $project_status
     */
    public function setProjectStatus($project_status)
    {
        $this->project_status = $project_status;
    }

    public function getProject()
    {
        return $this->project_name . ' have status ' . $this->project_status . ' and this is project of ' . $this->getCompanyName() . ' company';
    }
}