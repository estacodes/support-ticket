<?php

namespace App\Http\Controllers;

use App\Services\ServiceClass;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    private $service;
    public function __construct(ServiceClass $serviceClass) {
        $this->service = $serviceClass;
    }

    /**
     * Get all support tickets
     *
     */
    public function viewAllSupportTickets() {
        return $this->service->getAllTicketsForCurrentUser();
    }


    /**
     * View a single support ticket
     * @param $ticketId
     * @return \Illuminate\Http\Response
     */
    public function viewTicket($ticketId) {
        $ticket = $this->service->getSingleSupportTicket($ticketId);
        if(is_null($ticket))
            return response(["response" => "Ticket not found", "data"=> null], 404);

        return response(["response" => "Ticket query successful", "data"=> $ticket], 200);
    }


    /**
     * Open a new support ticket
     * @param Request $request
     * @bodyParam name string
     * @bodyParam email string
     * @bodyParam department string
     * @bodyParam related_service string
     * @bodyParam priority string low|medium|high
     * @bodyParam status string open|replied|close
     * @bodyParam subject string
     * @bodyParam message string
     * @bodyParam attachment file
     * @return \Illuminate\Http\Response
     */
    public function openSupportTicket(Request $request) {
        $response = $this->service->submitNewSupportTicket($request->all());

        if($response === "created") {
            return response(["response" => "Support Ticket Submitted successfully", "data" => null], 200);
        }elseif ($response === "failed"){
            return response(["response" => "Error occurred: unable to create support ticket", "data" => null], 500);
        }else{
            return response(["response" => $response, "data" => null], 400);
        }
    }


    /**
     * Update support ticket
     * @param Request $request
     * @bodyParam ticket_id string uniqueID of ticket to be updated
     * @bodyParam name string
     * @bodyParam email string
     * @bodyParam department string
     * @bodyParam related_service string
     * @bodyParam priority string low|medium|high
     * @bodyParam status string open|replied|close
     * @bodyParam subject string
     * @bodyParam message string
     * @bodyParam attachment file
     * @return \Illuminate\Http\Response
     */
    public function updateTicket(Request $request) {
        $response = $this->service->updateExistingSupportTicket($request->all());

        if($response === "updated") {
            return response(["response" => "Support Ticket updated successfully", "data" => null], 200);
        }elseif ($response === "failed") {
            return response(["response" => "Error occurred: unable to update support ticket", "data" => null], 500);
        }else{
            return response(["response" => $response, "data" => null], 400);
        }
    }


    /**
     * Assign or Transfer ticket to support agent
     * @param Request $request
     * @bodyParam agent_email string
     * @bodyParam ticket_id string uniqueID of ticket to be assigned
     * @return \Illuminate\Http\Response
     *
     */
    public function assignOrTransferTicket(Request $request) {
        $response = $this->service->assignOrTransferTicketToServiceAgent($request->only(['agent_email','ticket_id']));
        return response(["response" => $response[1], "data" => null], !$response[0] ? 400 : 200);
    }


    /**
     * Close support ticket
     * @param $ticketId
     * @bodyParam agent_email string
     * @bodyParam ticket_id string uniqueID of ticket to be assigned
     * @return \Illuminate\Http\Response
     *
     */
    public function closeTicket($ticketId) {
        $response = $this->service->closeSupportTicket($ticketId);
        return response(["response" => $response[1], "data" => null], !$response[0] ? 400 : 200);
    }
}
