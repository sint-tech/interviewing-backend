<?php

namespace Database\Seeders;

use Domain\JobTitle\Models\JobTitle;
use Illuminate\Database\Seeder;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Software Engineer',
            'ML Ops',
            'DevOps',
            'Product Owner',
            "Sales Representative",
            "Marketing Specialist",
            "Graphic Designer",
            "Customer Service Representative",
            "Financial Analyst",
            "HR Generalist",
            "Content Writer",
            "Social Media Manager",
            "UX/UI Designer",
            "Executive Assistant",
            "Legal Assistant",
            "Research Analyst",
            "Event Coordinator",
            "Customer Success Manager",
            "Accountant",
            "Supply Chain Manager",
            "Public Relations Specialist",
            "Sales Engineer",
            "E-commerce Manager",
            "Talent Acquisition Specialist",
            "UI/UX Developer",
            "Logistics Coordinator",
            "Content Strategist",
            "Financial Planner",
            "Project Coordinator",
            "Interior Designer",
            "Procurement Specialist",
            "Business Development Manager",
            "Environmental Scientist",
            "Legal Secretary",
            "Customer Support Manager",
            "Supply Chain Analyst",
            "Brand Manager",
            "Social Worker",
            "Event Planner",
            "Content Marketing Manager",
            "Sustainability Coordinator"
        ];

        foreach ($data as $datum) {
            JobTitle::query()->firstOrCreate(['title' => $datum]);
        }
    }
}
