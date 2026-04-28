<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\TrackActiveShipmentsJob;

Schedule::job(new TrackActiveShipmentsJob)->everyFifteenMinutes();
