<?php

namespace App\Enums;

enum AttendanceStatus
{
    const PRESENT = "HADIR";
    const ABSENT = "ALPA";
    const PERMIT = "IZIN";
    const SICK = "SAKIT";
    const OTHER = "LAINNYA";
}
