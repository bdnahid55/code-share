<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;

class ProductAjaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Product::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';

                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';

                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('productAjax');
    }

    // stotre data into database
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'detail' => 'required|min:4',
        ]);

        if ($validator->passes()) {
            Product::updateOrCreate([
                'id' => $request->product_id
            ],
            [
                'name' => $request->name,
                'detail' => $request->detail
            ]);

        return response()->json(['success'=>'Product saved successfully.']);
        }else{
            return response()->json(['error'=>$validator->errors()->all()]);
        }
        

    }

    // Edit data
    public function edit($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    // Delete data
    public function destroy($id)
    {
        $delete = Product::find($id)->delete();

        // check data deleted or not
        if ($delete) {
            $success = true;
            $message = "Data deleted successfully";
        } else {
            $success = true;
            $message = "Data not found";
        }

        //  return response
        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }

}
