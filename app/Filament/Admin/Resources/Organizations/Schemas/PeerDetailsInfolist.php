<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ActionsEntry;
use Filament\Infolists\Infolist;
use Filament\Actions\Action;
use App\Models\OrganizationPeerEvaluator;
use App\Models\Student;

class PeerDetailsInfolist
{
    public static function configure(Infolist $infolist, $organization): Infolist
    {
        // Get all unique peer evaluators for this organization
        $peerEvaluators = OrganizationPeerEvaluator::where('organization_id', $organization->id)
            ->with('evaluatorStudent')
            ->get()
            ->groupBy('evaluator_student_id');

        return $infolist->components([
            \Filament\Infolists\Components\Card::make()
                ->schema([
                    \Filament\Infolists\Components\Section::make('Peer Evaluators')
                        ->schema([
                            RepeatableEntry::make('peer_evaluators')
                                ->label('Peer Evaluators')
                                ->state(function () use ($peerEvaluators) {
                                    return $peerEvaluators->map(function ($assignments, $evaluatorId) {
                                        $evaluator = $assignments->first()->evaluatorStudent;
                                        return [
                                            'evaluator_name' => $evaluator ? $evaluator->name : 'Unknown',
                                            'evaluatee_count' => $assignments->count(),
                                            'evaluator_id' => $evaluatorId,
                                        ];
                                    })->values()->toArray();
                                })
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('evaluator_name')->label('Peer Evaluator'),
                                        TextEntry::make('evaluatee_count')->label('Students to Evaluate'),
                                        ActionsEntry::make('actions')
                                            ->label('Actions')
                                            ->actions([
                                                Action::make('edit')
                                                    ->label('Edit')
                                                    ->icon('heroicon-o-pencil')
                                                    ->color('primary'),
                                                Action::make('remove')
                                                    ->label('Remove')
                                                    ->icon('heroicon-o-trash')
                                                    ->color('danger'),
                                            ]),
                                    ]),
                                ])
                                ->columns(1),
                        ])
                        ->columns(1),
                ])
                ->columnSpanFull()
                ->extraAttributes(['class' => 'mb-6']),
        ]);
    }
}
