<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContractResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \App\Models\Contact $contact */
        // $contact = new Contact($data);
        // $contact->user_id = $user->id;
        // $contact->save();

        // Or
        $contact = $user->contacts()->create($data);

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }
}
