<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\poll;
use Illuminate\Support\Facades\Auth;
use App\Models\choice;
use App\Models\vote;

class pollController extends Controller

{
    public $auth;


    public function __construct()
    {
        $this->auth = auth()->user();
        $this->middleware('auth:api', ['except' => ['login', 'password_generator']]);
    }


    public function create(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required',
            'choices' => 'required'
        ]);

        // dd($this->auth['role']);
        if ($this->auth['role'] == 'user') {
            return response()->json(['error' => 'Kamu ga punya akses untuk ini'], 422);
        }

        $request['created_by'] = $this->auth['id'];
        $poll = poll::create($request->all()); //its contains data last inserted
        // dd($poll);
        // dd($request->all());
        foreach ($request['choices'] as $i => $key) {
            choice::create([
                'poll_id' => $poll['id'],
                'choices' => $key
            ]);
        }

        return response()->json(['message' => 'Success Creating Polling, Nice!']);
    }

    public function get()
    {
        $poll = poll::with('user')->get()->toArray();
        $dataResult = [];
        foreach ($poll as $fPoll) {
            $voted = vote::where('poll_id', $fPoll['id'])->get();
            /**
             * poll_id, user_id, choice_id, division_id choices : []
             */

            //get division
            $division_id = null;
            $data = [];
            foreach ($voted as $fVoted) { //1 => 1
                if ($division_id != $fVoted['division_id']) {
                    $division_id = $fVoted['division_id'];
                }
                $data[$division_id] = [];
            }

            //choice
            $result = [];
            foreach ($voted as $fVoted) {
                $data[$fVoted['division_id']][] = $fVoted['choice_id'];
                //mendapatkan apa saja yang ada di choice
                $result[$fVoted['choice_id']] = null;
            }

            //hitung
            foreach ($data as $fData) {
                $length = count($fData);
                foreach ($fData as $ffData) {
                    $result[$ffData] += 1 / $length;
                }
            }

            $eachNilai = array_sum($result);
            foreach ($result as $key => $fResult) {

                $nilai = $fResult / $eachNilai * 100;
                $result[$key] = $nilai . "%";
                // dd('DONE', $fResult, $eachNilai, $nilai, $result);
            }

            //last
            // dd($choiced);
            $collectResult = [];
            foreach ($result as $key => $fResult) {
                $choice = choice::find($key);
                // $result[$key] = 
                unset($result[$key]);
                // $result[$choice['choices']] = $fResult;
                $collectResult[] = ["key" => $choice['choices'], "val" => $fResult];
            }

            // dd($result);

            $dataResult[] = array_merge($fPoll, ['detail' => $collectResult]);
        }
        return response()->json(['collection' => $dataResult]);
        // dd($poll, count($poll));
    }
    public function detail($id)
    {
        $poll = poll::with('user')->find($id)->toArray();
        $voted = vote::where('poll_id', $id)->get();
        /**
         * poll_id, user_id, choice_id, division_id
         */

        //get division
        $division_id = null;
        $data = [];
        foreach ($voted as $fVoted) { //1 => 1
            if ($division_id != $fVoted['division_id']) {
                $division_id = $fVoted['division_id'];
            }
            $data[$division_id] = [];
        }

        //choice
        $result = [];
        foreach ($voted as $fVoted) {
            $data[$fVoted['division_id']][] = $fVoted['choice_id'];
            //mendapatkan apa saja yang ada di choice
            $result[$fVoted['choice_id']] = null;
        }

        //hitung
        foreach ($data as $fData) {
            $length = count($fData);
            foreach ($fData as $ffData) {
                $result[$ffData] += 1 / $length;
            }
        }

        $eachNilai = array_sum($result);
        foreach ($result as $key => $fResult) {

            $nilai = $fResult / $eachNilai * 100;
            // $result[$key] = $nilai . "%";
            $result[$key] = $nilai . "%";
            // dd('DONE', $fResult, $eachNilai, $nilai, $result);
        }

        //last
        // dd($choiced);
        $resultCollection = [];
        foreach ($result as $key => $fResult) {
            $choice = choice::find($key);
            // $result[$key] = 
            unset($result[$key]);
            // $result[$choice['choices']] = $fResult;
            $resultCollection[] = ["key" => $choice['choices'],"val" => $fResult];
        }
        // dd($data, $result, $poll);
        return response()->json(['data' => array_merge($poll, ["detail" => $resultCollection])]);
    }

    public function vote(Request $request, $poll_id, $choices_id)
    {

        if ($this->auth == null) {
            return response()->json(['message' => 'Unauthorized']);
        }

        if ($this->auth['role'] == 'admin') {
            return response()->json(['message' => 'You cant contribute to make a voting']);
        }

        // $validate = $request->validate([
        //     'poll_id' => 'required',
        //     'choices_id' => 'required' 
        // ]);

        $hasVote = vote::where('poll_id', $poll_id)->where('user_id', $this->auth['id'])->first();
        if ($hasVote) {
            return response()->json(['message' => 'You has voted for this polling']);
        }

        vote::create([
            'user_id' => $this->auth['id'],
            'division_id' => $this->auth['division_id'],
            'poll_id' => $poll_id,
            'choice_id' => $choices_id
        ]);

        return response()->json(['message' => 'Succes voting!'], 200);
    }

    public function delete($id)
    {
        if ($this->auth == null) {
            return response()->json(['message' => 'Unauthorized']);
        }

        if ($this->auth['role'] != 'admin') {
            return response()->json(['message' => 'You cant contribute to this']);
        }

        poll::find($id)->delete();
        return response()->json(['message' => 'Poll has removed']);
    }
}
