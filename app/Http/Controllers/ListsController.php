<?php

namespace App\Http\Controllers;

use App\Lists;
use App\Board;
use Illuminate\Http\Request;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class ListsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Board $board)
    {
        return view('lists.create', ['board'=>$board->id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, ['title'=>'required']);

        $board = Board::find($request->get('board'));
        if(Gate::denies('board-owner', $board))
        {
            return redirect('/');
        }

        count($board->last_list) == 0 ? $order = 1:  $order = $board->last_list[0]->order+1;

        $data = [
                'title' => $request->get('title'),
                'board_id' => $request->get('board'),
                'order' => $order
            ];

        try{
            Lists::create($data);
        }
        catch(QueryException $e){
            return response()->view('errors.500', [], 500);
        }

        return redirect(route('boards.show', $board))->with(['success' => 'List was created successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lists  $lists
     * @return \Illuminate\Http\Response
     */
    public function edit(Lists $list)
    {
        $board = $list->board;
        if(Gate::denies('board-owner', $board))
        {
            return redirect('/');
        }

        return view('lists.edit', ['list'=>$list]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lists  $lists
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lists $list)
    {
        $this->validate($request, ['title'=>'required|min:2']);

        $board = $list->board;
        if(Gate::denies('board-owner', $board))
        {
            return redirect('/');
        }

        try{
            $list->update(['title' => $request->get('title')]);
        }
        catch(QueryException $e){
            return response()->view('errors.500', [], 500);
        }
        return redirect(route('boards.show', $board))->with(['success' => 'List was updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lists  $lists
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lists $list)
    {
        $board = $list->board;
        if(Gate::denies('board-owner', $board))
        {
            return redirect('/');
        }

        try{
            $list->delete();
        }
        catch(QueryException $e){
            return response()->view('errors.500', [], 500);
        }
        return redirect(route('boards.show', $board))->with(['success' => 'List was deleted successfully.']);
    }


    public function move(Request $request, Lists $list)
    {

        $board = $list->board;
        if(Gate::denies('board-owner', $board))
        {
            return redirect('/');
        }

        $this->validate($request, ['order' => 'required|integer|min:1']);

        try{

            $otherList = Lists::where('order', '=', $request->get('order'))->first();

            $otherList->update(['order' => $list->order]);

            $list->update(['order' => $request->get('order')]);
        }

        catch(QueryException $e){
            return response()->view('errors.500', [], 500);
        }
        return redirect(route('boards.show', $board))->with(['success' => 'List was moved successfully.']);
    }
}
