<?php

namespace App\Controller\V1;

use App\Contract\Request\ValidatedRequest;
use App\Model\Spice;
use App\Request\CreateSpiceRequest;
use App\Request\MassUpdateSpiceRequest;
use App\Request\PatchSpiceRequest;
use App\Request\UpdateSpiceRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WouterJ\EloquentBundle\Facade\Db;
use App\Enum\Status;

class SpiceController extends AbstractController
{
    #[Route('/spice', name: 'add_spice', methods: ['POST'])]
    public function addSpice(CreateSpiceRequest $request): Response
    {
        if (!$request->validate()) {
            return $this->json($request->errorMessages(), 422);
        }

        $spice = new Spice();
        $spice->name = $request->name;
        $spice->status = $request->status;

        $spice->save();

        return $this->json($spice, 201);
    }

    #[Route('/spice', name: 'update_spice', methods: ['PUT'])]
    public function updateSpice(UpdateSpiceRequest $request): Response {
        return $this->updateSpiceModel($request);
    }

    #[Route('/spice', name: 'patch_spice', methods: ['PATCH'])]
    public function patchSpice(PatchSpiceRequest $request): Response {
        return $this->updateSpiceModel($request);
    }

    protected function updateSpiceModel(ValidatedRequest $request): Response
    {
        if (!$request->validate()) {
            return $this->json($request->errorMessages(), 422);
        }

        $spice = Spice::find($request->id);
        foreach ($request->all() as $property => $value) {
            $spice->{$property} = $value;
        }

        $spice->save();

        return $this->json($spice);
    }

    #[Route('/spice/{id}', name: 'get_spice', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getSpice(string $id): Response
    {
        $spice = Spice::find($id);

        if (is_null($spice)) {
            return $this->json(['message' => 'Spice not found'], 404);
        }

        return $this->json($spice);
    }

    #[Route('/spice/{id}', name: 'delete_spice', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteSpice(string $id): Response
    {
        $spice = Spice::find($id);

        if (is_null($spice)) {
            return $this->json(['message' => 'Spice not found'], 404);
        }

        $spice->delete();

        return $this->json([], 204);
    }

    #[Route(
        '/spice/list/{status?}',
        name: 'get_spice_list',
        methods: ['GET'],
        requirements: ['status' => '(full|runningOut|outOfStock)']
    )]
    public function getSpiceList(?string $status, Request $request): Response
    {
        $query = $request->query->get('q', '');
        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int) $request->query->get('perPage', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $spices = Spice::where('name', 'like', '%' . $query . '%');
        if (!is_null($status)) {
             $spices->where('status', str_replace('_', ' ', $status));
        }
        $total = $spices->count();
        $spices = $spices->forPage($page, $perPage)->get();

        $response = [
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'items' => $spices
        ];

        return $this->json($response);
    }

    #[Route('/spices', name: 'mass_update_spices', methods: ['PATCH'])]
    public function massUpdateSpices(MassUpdateSpiceRequest $request): Response
    {
        if (!$request->validate()) {
            return $this->json($request->errorMessages(), 422);
        }

        Spice::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return $this->json(Spice::find($request->ids), 207);
    }

    #[Route('/spice/list/statuses', name: 'spice_status_list', methods: ['GET'])]
    public function spiceStatusesList(): Response {
        $defaultStatuses = array_combine(Status::values(), [0,0,0]);
        $statuses = Spice::groupBy('status')
            ->select([Db::raw('count(id) as count'), 'status'])
            ->get()
            ->pluck('count', 'status')->all();
        $statuses = $statuses + $defaultStatuses;
        $statuses['all'] = array_sum(array_values($statuses));

        return $this->json($statuses);
    }
}