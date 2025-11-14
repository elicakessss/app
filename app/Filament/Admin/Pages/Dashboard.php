<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
	/**
	 * Use a 12-column grid so widgets can use 1-12 spans. This lets us place two
	 * widgets side-by-side by giving each a columnSpan of 6.
	 */
	public function getColumns(): int | array
	{
		return 12;
	}

}
