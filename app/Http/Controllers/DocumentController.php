<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyDocumentRequest;
use App\Http\Requests\ListDocumentRequest;
use App\Http\Requests\ShowDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(ListDocumentRequest $request): AnonymousResourceCollection
    {
        return DocumentResource::collection(
            resource: Document::ownedByUser()->expiringSoon()->get()
        );
    }

    public function store(StoreDocumentRequest $request): DocumentResource
    {
        $uploadedDocumentPath = Storage::putFile('documents', $request->file('document'));

        $document = $request
            ->user()
            ->documents()
            ->create(
                [
                    'name' => $request->validated('name'),
                    'path' => $uploadedDocumentPath,
                    'expires_at' => Carbon::now()->addMonth(1),
                ]
            );

        return DocumentResource::make($document);
    }

    public function show(ShowDocumentRequest $request, Document $document): DocumentResource
    {
        return DocumentResource::make($document);
    }

    public function destroy(DestroyDocumentRequest $request, Document $document): ?bool
    {
        return $document->delete();
    }
}
