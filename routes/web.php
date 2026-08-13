<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $tables = [];

    $tableNames = Schema::getTableListing();

    foreach ($tableNames as $tableName) {
        $columns = Schema::getColumnListing($tableName);
        $rows = DB::table($tableName)->limit(100)->get();

        $tables[] = [
            'name' => $tableName,
            'columns' => $columns,
            'rows' => $rows,
            'count' => DB::table($tableName)->count(),
        ];
    }

    return view('welcome', compact('tables'));
});
