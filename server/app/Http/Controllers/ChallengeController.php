<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreChallengeRequest;
use App\Models\Key;
use App\Models\Challenge;


class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChallengeRequest $request)
    {
        $input = $request->validated();

        // Hash the input pubkey and check if it exists in the keys table
        $hash = hash('sha3-256', $input['pubkey']);
        $key = Key::where('hash', $hash)->firstOr(function() use ($input, $hash) {
            // No existing key was found, so create a new key model
            $newKey = new Key;
            $newKey->pubkey = $input['pubkey'];
            $newKey->hash = $hash;
            $newKey->save();

            return $newKey;
        });

        // Increment the number of requested challenges
        $key->challenges_requested += 1;
        $key->save();

        // Create a random string
        // TODO: Handle the very unlikely edge case that a random string is not unique?
        // We are already using a unique index on the challenge column, but if a non-unique random string was generated this API call would fail with a 500 error
        $randomString = base64_encode(random_bytes(128));

        // Create a new challenge object
        $challenge = new Challenge;
        $challenge->key_id = $key->id;
        $challenge->string = $randomString;
        $challenge->created_by_ip = $request->ip();
        $challenge->save();

        // Return the random string so it can be used in a subsequent API call
        return response()->json(['challenge' => $randomString], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
