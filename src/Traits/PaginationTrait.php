<?php


namespace Zaber04\LumenApiResources\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

trait PaginationTrait
{
    /**
     * Get pagination parameters from the request.
     *
     * @param  Request  $request
     * @return array
     */
    private function getPaginationParams(Request $request): array
    {
        // Default values
        $defaultPage      = 1;
        $defaultPerPage   = 20;
        $defaultSortField = 'created_at';
        $defaultSortOrder = 'desc';

        // Extracting values from the request or using defaults
        $page      = $request->filled('page') ? $request->input('page') : $defaultPage;
        $perPage   = $request->filled('per_page') ? $request->input('per_page') : $defaultPerPage;
        $sortField = $request->filled('sort_field') ? $request->input('sort_field') : $defaultSortField;
        $sortOrder = $request->filled('sort_order') ? $request->input('sort_order') : $defaultSortOrder;

        return [
            'page'       => $page,
            'per_page'   => $perPage,
            'sort_field' => $sortField,
            'sort_order' => $sortOrder,
        ];
    }

    /**
     * Validate pagination parameters.
     *
     * @param  Request  $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validatePagination(Request $request): void
    {
        $this->validate($request, [
            'page'       => 'integer|min:1',
            'per_page'   => 'integer|min:1|max:100',
            'sort_field' => Rule::in(['created_at', 'ip', 'id']),
            'sort_order' => Rule::in(['asc', 'desc']),
        ]);
    }
}
