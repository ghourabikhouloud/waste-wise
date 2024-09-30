<?php

namespace App\Http\Controllers;
use App\Models\WasteTip;  // Assurez-vous que cette ligne est présente
use App\Models\AdviceType;  // Assurez-vous que cette ligne est présente

use Illuminate\Http\Request;

class WasteTipController extends Controller
{

   // Afficher le formulaire de création
   public function index()
{
    $adviceTypes = AdviceType::all();
    $wasteTips = WasteTip::with('adviceType')->get(); // Récupère tous les WasteTips avec leur AdviceType

    return view('admin.WasteTips', compact('adviceTypes', 'wasteTips'));
}

   // Stocker le Waste Tip
   public function store(Request $request)
   {
       // Validation des données
       $request->validate([
           'title' => 'required|string|max:255',
           'content' => 'required|string',
           'advice_type_id' => 'required|exists:advice_types,id', // Assure-toi que l'ID existe
           'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation pour l'image
       ]);
   
       // Gestion de l'upload de l'image
       $imagePath = null; // Initialisation du chemin de l'image
       if ($request->hasFile('image')) {
           $imagePath = $request->file('image')->store('images/waste_tips', 'public'); // Stockage de l'image dans le dossier public
       }
   
       // Création d'un nouveau WasteTip
       WasteTip::create([
           'title' => $request->title,
           'content' => $request->content,
           'advice_type_id' => $request->advice_type_id,
           'author' => auth()->user()->id, // Par exemple, si tu veux attribuer l'auteur
           'Creation_date' => now(), // Date de création
           'image' => $imagePath, // Enregistrement du chemin de l'image
       ]);
   
       return redirect()->route('admin.WasteTips')->with('success', 'Waste Tip added successfully!');
   }
   public function update(Request $request, $id)
   {
       $request->validate([
           'title' => 'required|string|max:255',
           'content' => 'required|string|max:1000',
           'advice_type_id' => 'required|exists:advice_types,id',
           'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
       ]);
        $wasteTip = WasteTip::findOrFail($id);
        $wasteTip->title = $request->input('title');
        $wasteTip->content = $request->input('content');
        $wasteTip->advice_type_id = $request->input('advice_type_id');
          if ($request->hasFile('image')) {
           if ($wasteTip->image) {
               \Storage::disk('public')->delete($wasteTip->image);
           }
              $imagePath = $request->file('image')->store('waste_tips', 'public');
           $wasteTip->image = $imagePath;
       }
          $wasteTip->save();
          return redirect()->route('admin.WasteTips')->with('success', 'Waste Tip updated successfully!');
   }
   

    public function show($id)
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
        // Récupération du WasteTip par ID
        $wasteTip = WasteTip::findOrFail($id);
        
        // Suppression du WasteTip
        $wasteTip->delete();
    
        return redirect()->route('admin.WasteTips')->with('success', 'Waste Tip deleted successfully!');
    }
}
