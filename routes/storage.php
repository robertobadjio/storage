<?php

Route::post('/upload', 'StorageController@upload');
Route::get('/{fid}', 'StorageController@get');
Route::delete('/{fid}', 'StorageController@remove');