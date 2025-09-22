<table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom catégorie</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ( $categories as $c)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm font-mono text-gray-700">{{ $c['code'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $c['name'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $c['description'] }}</td>
                                <td class="px-4 py-2 text-sm text-right">
                                    <a href="#" class="text-primary-700 hover:text-primary-600">Détails</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
</table>