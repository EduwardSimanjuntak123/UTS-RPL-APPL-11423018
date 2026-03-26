<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AnalyticsLog;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Schedule appointment dengan validasi ketersediaan
     */
    public function scheduleAppointment(array $data)
    {
        // Cek apakah doctor sudah punya appointment di waktu yang sama
        $existingAppointment = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->exists();

        if ($existingAppointment) {
            throw new \Exception('Doctor tidak tersedia pada waktu tersebut');
        }

        $appointment = Appointment::create(array_merge($data, [
            'status' => 'scheduled'
        ]));

        AnalyticsLog::create([
            'event_type' => 'appointment-scheduled',
            'entity_type' => 'appointment',
            'entity_id' => $appointment->id,
            'description' => 'Appointment scheduled',
        ]);

        return $appointment;
    }

    /**
     * Reschedule appointment
     */
    public function rescheduleAppointment(Appointment $appointment, Carbon $newDate)
    {
        // Cek ketersediaan di waktu baru
        $existingAppointment = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $newDate)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($existingAppointment) {
            throw new \Exception('Doctor tidak tersedia pada waktu baru');
        }

        $appointment->update(['appointment_date' => $newDate]);

        AnalyticsLog::create([
            'event_type' => 'appointment-rescheduled',
            'entity_type' => 'appointment',
            'entity_id' => $appointment->id,
            'description' => 'Appointment rescheduled',
        ]);

        return $appointment;
    }

    /**
     * Cancel appointment
     */
    public function cancelAppointment(Appointment $appointment, $reason = null)
    {
        $appointment->update([
            'status' => 'cancelled',
            'notes' => $reason
        ]);

        AnalyticsLog::create([
            'event_type' => 'appointment-cancelled',
            'entity_type' => 'appointment',
            'entity_id' => $appointment->id,
            'description' => 'Appointment cancelled: ' . ($reason ?? 'No reason provided'),
        ]);

        return $appointment;
    }

    /**
     * Get doctor's available slots
     */
    public function getAvailableSlots($doctorId, Carbon $date, $duration = 30)
    {
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->pluck('appointment_date')
            ->toArray();

        $dayStart = $date->copy()->setHour(9)->setMinute(0);
        $dayEnd = $date->copy()->setHour(17)->setMinute(0);

        $availableSlots = [];
        $current = $dayStart->copy();

        while ($current < $dayEnd) {
            $slotBooked = false;
            foreach ($appointments as $appointmentTime) {
                $appointmentTime = Carbon::parse($appointmentTime);
                if ($current->diffInMinutes($appointmentTime) < $duration) {
                    $slotBooked = true;
                    break;
                }
            }

            if (!$slotBooked) {
                $availableSlots[] = $current->copy()->format('H:i');
            }

            $current->addMinutes($duration);
        }

        return $availableSlots;
    }

    /**
     * Get appointment statistics
     */
    public function getAppointmentStats($doctorId = null)
    {
        $query = Appointment::query();

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        return [
            'total' => $query->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'scheduled' => $query->where('status', 'scheduled')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'no_show' => $query->where('status', 'no-show')->count(),
        ];
    }
}
