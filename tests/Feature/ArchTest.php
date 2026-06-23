<?php

declare(strict_types=1);

arch('app code declares strict types')
    ->expect('App')
    ->toUseStrictTypes()
    ->ignoring('App\Providers\TelescopeServiceProvider');

arch('no leftover debugging helpers')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();

arch('models extend the read-only base model')
    ->expect('App\Models')
    ->toExtend('App\Models\BaseModel')
    ->ignoring(['App\Models\BaseModel', 'App\Models\ItemSkillTree', 'App\Models\User']);

arch('api controllers are suffixed Controller')
    ->expect('App\Http\Controllers\Api\V1')
    ->toHaveSuffix('Controller');

arch('resources extend JsonResource')
    ->expect('App\Http\Resources\V1')
    ->toExtend('Illuminate\Http\Resources\Json\JsonResource');

arch('enums are backed enums')
    ->expect('App\Enums')
    ->toBeEnums()
    ->ignoring('App\Enums\Concerns');
