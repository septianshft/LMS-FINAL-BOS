<?php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $items = CartItem::with('course')->where('user_id', Auth::id())->get();
        return view('front.cart', compact('items'));
    }

    public function store(Request $request, Course $course)
    {
        $item = CartItem::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
        ], [
            'quantity' => 1,
        ]);
        return back()->with('success', 'Course added to cart');
    }

    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id == Auth::id()) {
            $cartItem->delete();
        }
        return back();
    }
}
