<?php

namespace App\Http\Controllers;

use App\Http\Traits\GeneralTrait;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Expectation;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $msg = "all products are Right Here";
            $data = Review::all();
            return $this->successResponse($data, $msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = $request->validate([
            'comment' => 'required|regex:/[a-zA-Z\s]+/',
            'rating' => 'required|numeric',
            'user_id' => 'required',
            'product_id' => 'required'
        ]);
        try {
            // create a new review object
            $review = new Review();
            $review->comment = $validator['comment'];
            $review->rating = $validator['rating'];
            $review->user_id = Auth::id();
            $review->product_id = $validator['product_id'];
            // save the new review to the database
            $review->save();
            $msg = "done create review for product";
            return $this->successResponse($review, $msg, 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
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
    // Define function that reviewed a specific user of a specific product
    //إرجاع تقييم مستخدم معين على منتج معين
    public function getUserRatingForProduct($user_id, $product_id)
    {
        try {
            $review = Review::where('user_id', $user_id)
                ->where('product_id', $product_id)
                ->first();
            if ($has_reviewed = !is_null($review)) {
                $msg = "done show review for product by user";
            } else {
                $msg = "user dont have any review for this a product";
            }

            return $this->successResponse($review, $msg, 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
    //function return all Users reviewed specific product
    // إرجاع كل المستخدمين الذين قيمو منتج معين
    public function getUsersByProduct(Request $request, $product_id)
    {
        try {
            $reviews = Review::where('product_id', $product_id)->get();
            $users = [];
            foreach ($reviews as $review) {
                $user = User::find($review->user_id);
                if ($user) {
                    $users[] = $user;
                }
            }
            $msg = "Success Get all users who have rated a particular product";
            return $this->successResponse($users, $msg, 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
    // Define function to get all reviews for a product
    public function getAllReviewsForProduct(Request $request, $product_id)
    {
        try {
            // Get the product with reviews
            $product = Product::with('review')->findOrFail($product_id);

            // Get the reviews for the product
            $reviews = $product->review;
            // Return the reviews in the response
            $msg = "done get all reviews for a product";
            return $this->successResponse($reviews, $msg, 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
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
