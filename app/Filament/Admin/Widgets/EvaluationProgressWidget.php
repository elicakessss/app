<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use App\Models\Student;
use App\Models\User;
use App\Models\Rank;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EvaluationProgressWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $totalOrganizations = Organization::count();
        $totalStudents = Student::count();
        $totalUsers = User::count();
        
        // Count evaluations completed (finalized ranks)
        $completedEvaluations = Rank::where('status', 'finalized')->count();
        
        return [
            Stat::make('🏛 Total Organizations', $totalOrganizations)
                ->description('Active student organizations')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary'),
                
            Stat::make('👨‍🎓 Total Students', $totalStudents)
                ->description('Enrolled in the system')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),
                
            Stat::make('👥 Total Users', $totalUsers)
                ->description('Admin and adviser accounts')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),
                
            Stat::make('✅ Evaluations Completed', $completedEvaluations)
                ->description('Rankings finalized')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('warning'),
        ];
    }
}
