<?php

namespace App\Http\Controllers;

use App\Models\Translations;
use Illuminate\Http\Request;

class TmController extends Controller
{
    public function insertTm(Request $request, $source, $target)
    {
        $request->validate([
            'sentence' => 'required',
            'translation' => 'required',
            'clientId' => 'required',
            'domain' => 'required'
        ]);
        Translations::updateOrCreate([
            'source' => $source,
            'target' => $target,
            'sentence' => $request->sentence,
            'translation' => $request->translation,
            'clientId' => $request->clientId,
            'domain' => $request->domain
        ],[
            'translation' => $request->translation
        ]);
        return response()->json([
            'data' => 'success'
        ]);
    }

    public function getTM (Request $request, $source, $target)
    {
        $request->validate([
            'sentence' => 'required',
            'clientId' => 'required',
            'domain' => 'required'
        ]);

        $translations = Translations::where('sentence','LIKE', '%'.$request->sentence.'%')
                ->where('clientId', $request->clientId)
                ->where('domain', $request->domain)
                ->where('source', $source)
                ->where('target', $target)
                ->get();
        $translations->transform(function($data) use($request){
            $data->percentage = $this->getPrediction($request->sentence, $data->sentence);
            return $data;
        });
        return response()->json(['data'=>$translations->all()]);
    }

    public function getPrediction ($source, $translation)
    {
         similar_text($source, $translation, $percent);
         return $percent;
    }
}
