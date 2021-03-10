<?php


namespace App\Backend;


use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportTicketRepository
{
    const FOLDER_PATH = "attachments";
    const TICKET_OPENED = "open";
    const TICKET_REPLIED = "replied";
    const TICKET_CLOSED = "close";

    public function getAllTicketsForCurrentUser() {
        return SupportTicket::with('user')->
                where("assignee", auth("api")->user()->id)->get();
    }

    public function getSingleSupportTicket($ticket_id) {
        return SupportTicket::where("assignee", auth("api")->user()->id)
            ->where("ticket_id", $ticket_id)->first();
    }

    public function submitNewSupportTicket($data) {
        //upload file attachment, if any
        if(!is_null($data["attachment"])) {
            $attachment = $this->uploadTicketAttachment($data['attachment']);
        }
//dd($attachment);

        //insert data
        SupportTicket::create([
            "ticket_id" => Str::orderedUuid(),
            "name" => $data["name"],
            "email" => $data["email"],
            "department" => $data["department"],
            "related_service" => $data["related_service"],
            "priority" => $data["priority"],
            "status" => self::TICKET_OPENED,
            "subject" => $data["subject"],
            "message" => $data["message"],
            "attachment" => isset($attachment) ? $attachment : null
        ]);

        return "created";
    }

    private function uploadTicketAttachment(UploadedFile $file, $oldAttachment=null) {
        $attachment = uniqid().".".$file->getClientOriginalExtension();
        Storage::putFileAs(self::FOLDER_PATH, $file, $attachment);

        if(!is_null($oldAttachment))
            Storage::delete(self::FOLDER_PATH.DIRECTORY_SEPARATOR.$oldAttachment);

        return $attachment;
    }

    public function updateExistingSupportTicket($data) {
        $ticket = SupportTicket::where('assignee', auth()->user()->id)->where('ticket_id', $data['ticket_id'])->first();

        //upload file attachment, if any
        if(!is_null($data["attachment"])) {
            $attachment = $this->uploadTicketAttachment($data['attachment'], $ticket->attachment);
        }


        //update ticket
        $ticket->update([
            "name" => $data["name"],
            "email" => $data["email"],
            "department" => $data["related_service"],
            "related_service" => $data["name"],
            "priority" => $data["priority"],
            "subject" => $data["subject"],
            "message" => $data["message"],
            "attachment" => isset($attachment) ?: null
        ]);

        return "updated";
    }

    public function assignOrTransferTicketToServiceAgent($user_email, $ticket_id) {
        $user = User::where('email', $user_email)->first();
        $ticket = SupportTicket::where('ticket_id', $ticket_id)->first();

        if($ticket->status === self::TICKET_CLOSED) {
            return [false, "Request failed: support ticket already closed"];
        }

        $ticket->assignee = $user->id;
        $ticket->updated_at = Carbon::now();
        $ticket->save();
        return [true, "Support ticket updated successfully"];
    }

    public function closeSupportTicket($ticket_id) {
        $ticket = SupportTicket::where('ticket_id', $ticket_id)->first();

        $ticket->status = self::TICKET_CLOSED;
        $ticket->updated_at = Carbon::now();
        $ticket->save();
        return [true, "Support ticket closed successfully"];
    }
}
