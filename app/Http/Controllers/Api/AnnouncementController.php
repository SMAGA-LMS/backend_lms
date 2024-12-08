<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest\AddNewAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    private $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    // LMS-154
    public function store(Request $request)
    {
        //
    }

    public function createNewAnnouncement(array $validatedRequest)
    {
        try {
            $data = [
                'title' => $validatedRequest['title'],
                'description' => $validatedRequest['description'],
                'file' => $validatedRequest['file'],
                'author_id' => $validatedRequest['author_id'],
            ];
            $announcementID = $this->announcement->insertNewAnnouncement($data);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create new announcement. " . $e->getMessage());
        }

        $announcement = $this->announcement->getAnnouncementByID($announcementID);
        return $announcement;
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        //
    }
}
