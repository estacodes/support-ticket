<?php


namespace App\Services;


use App\Backend\SupportTicketRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceClass
{
    private $repository;
    public function __construct(SupportTicketRepository $repository) {
        $this->repository = $repository;
    }

    public function getAllTicketsForCurrentUser() {
        return response(["response"=>"Tickets query successful", "data"=>$this->repository->getAllTicketsForCurrentUser()], 200);
    }

    public function getSingleSupportTicket($ticket_id) {
        return $this->repository->getSingleSupportTicket($ticket_id);
    }

    public function submitNewSupportTicket($data) {
        try {
            $validation = $this->validateTicketRequest($data);

            if($validation === true) {
                return $this->repository->submitNewSupportTicket($data);
            }

            return $validation;
        }catch(\Exception $e) {
            Log::error($e);
            return "failed";
        }
    }

    private function validateTicketRequest($data, $update=false) {
        $validate = Validator::make($data,[
            "name" => "required",
            "email" => "required|email",
            "department" => "required|string",
            "related_service" => "required|string",
            "priority" => "required|in:low,medium,high",
            "subject" => "required",
            "message" => "required",
            "attachment" => "nullable|file|mimes:jpg,jpeg,gif,png,pdf,zip,doc,docx",
            "ticket_id" => $update ? ["required", Rule::exists('support_tickets','ticket_id')->where('assignee',auth('api')->user()->id)] : "nullable"
        ]);

        if($validate->fails())
            return $validate->errors();

        return true;
    }

    public function updateExistingSupportTicket($data) {
        try {
            $validation = $this->validateTicketRequest($data, true);

            if($validation === true) {
                return $this->repository->updateExistingSupportTicket($data);
            }

            return $validation;
        }catch(\Exception $e) {
            Log::error($e);
            return "failed";
        }
    }

    public function assignOrTransferTicketToServiceAgent($request) {
        try{
            $validator = Validator::make($request,[
                'agent_email' => "required|exists:users,email",
                'ticket_id' => "required|exists:support_tickets,ticket_id"
            ]);

            if($validator->fails())
                return [false, $validator->errors()];

            return $this->repository->assignOrTransferTicketToServiceAgent($request['agent_email'], $request['ticket_id']);
        }catch(\Exception $e) {
            Log::error($e);
            return [false, "Error occurred: unable to update support ticket"];
        }
    }

    public function closeSupportTicket($ticketId) {
        try{
            $validator = Validator::make(['ticket_id' => $ticketId],[
                'ticket_id' => "required|exists:support_tickets,ticket_id"
            ]);

            if($validator->fails())
                return [false, $validator->errors()];

            return $this->repository->closeSupportTicket($ticketId);
        }catch(\Exception $e) {
            Log::error($e);
            return [false, "Error occurred: unable to update support ticket"];
        }
    }
}
