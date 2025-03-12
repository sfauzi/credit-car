<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-10">
    <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Hitung Angsuran Mobil</h2>

    <div class="grid grid-cols-1 gap-6">
        <!-- Form Input -->
        <div class="p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Form Input</h3>
            <form wire:submit.prevent="calculate" class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-gray-600 font-semibold">Nama Pelanggan:</label>
                    <input type="text" wire:model="customer_name"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                        placeholder="Masukkan nama pelanggan" required>
                    @error('customer_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-600 font-semibold">Harga Mobil (Rp):</label>
                    <input type="number" wire:model="car_price"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                        placeholder="Masukkan harga mobil" required>
                    @error('car_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-600 font-semibold">Lama Angsuran (bulan):</label>
                    <input type="number" wire:model="installment_months" min="1" max="60"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                        placeholder="Masukkan lama angsuran" required>
                    @error('installment_months')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit"
                    class="col-span-2 w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Hitung Angsuran
                </button>
            </form>
        </div>
    </div>

    <!-- Hasil Perhitungan -->
    <div class="grid grid-cols-1 gap-4">
        <div class="grid grid-cols-2 gap-4">
            @if ($monthly_installment)
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Hasil Perhitungan</h3>
                    <div class="space-y-2">
                        <p class="text-gray-600"><strong>Nama:</strong> {{ $customer_name }}</p>
                        <p class="text-gray-600"><strong>Harga Mobil (OTR):</strong> Rp
                            {{ number_format($car_price, 2, ',', '.') }}</p>
                        <p class="text-gray-600"><strong>Uang Muka (DP):</strong> Rp
                            {{ number_format($down_payment, 2, ',', '.') }}</p>
                        <p class="text-gray-600"><strong>Jangka Waktu:</strong> {{ $installment_months }} bulan</p>
                        <p class="text-gray-600"><strong>Pokok Utang:</strong> Rp
                            {{ number_format($car_price - $down_payment, 2, ',', '.') }}</p>
                        <p class="text-gray-600"><strong>Bunga ({{ $interest_rate * 100 }}%):</strong> Rp
                            {{ number_format($total_interest, 2, ',', '.') }}</p>
                        <p class="text-gray-600"><strong>Total Hutang:</strong> Rp
                            {{ number_format($total_loan, 2, ',', '.') }}</p>
                    </div>
                    <p class="text-gray-700 text-lg font-bold mt-4 text-center">
                        Angsuran Per Bulan: Rp {{ number_format($monthly_installment, 2, ',', '.') }}
                    </p>
                </div>

                <div class="mt-6 p-4 bg-gray-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Angsuran Jatuh Tempo per 14 Agustus
                        2024
                    </h3>
                    @php
                        $due_data = $this->getTotalDueInstallments();
                    @endphp
                    <table class="w-full border border-gray-300">
                        <thead class="bg-gray-300">
                            <tr>
                                <th class="p-2 border">Kontrak No</th>
                                <th class="p-2 border">Client Name</th>
                                <th class="p-2 border">Total Angsuran Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="p-2 border">{{ $due_data['contract_no'] }}</td>
                                <td class="p-2 border">{{ $due_data['client_name'] }}</td>
                                <td class="p-2 border">Rp
                                    {{ number_format($due_data['total_due_installment'], 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

        @if ($monthly_installment)
            <div class="grid grid-cols-1 gap-4">
                <div class="mt-6 p-4 bg-red-100 rounded-lg">
                    <h3 class="text-lg font-semibold text-red-700 mb-2">Denda Keterlambatan</h3>
                    @php $penalty_data = $this->getPenalty(); @endphp

                    @if (!empty($penalty_data['details']))
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="border px-4 py-2">KONTRAK NO</th>
                                    <th class="border px-4 py-2">CLIENT NAME</th>
                                    <th class="border px-4 py-2">INSTALLMENT NO</th>
                                    <th class="border px-4 py-2">HARI KETERLAMBATAN</th>
                                    <th class="border px-4 py-2">TOTAL DENDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penalty_data['details'] as $penalty)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $penalty['contract_no'] }}</td>
                                        <td class="border px-4 py-2">{{ $penalty['client_name'] }}</td>
                                        <td class="border px-4 py-2">{{ $penalty['installment_no'] }}</td>
                                        <td class="border px-4 py-2">{{ $penalty['late_days'] }} hari</td>
                                        <td class="border px-4 py-2">Rp
                                            {{ number_format($penalty['total_penalty'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="3" class="border px-4 py-2 text-right">TOTAL</td>
                                    <td class="border px-4 py-2">{{ $penalty_data['summary']['total_late_days'] }}
                                        hari</td>
                                    <td class="border px-4 py-2">Rp
                                        {{ number_format($penalty_data['summary']['total_penalty'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-green-600">Tidak ada keterlambatan pembayaran.</p>
                    @endif
                </div>
            </div>

        @endif

    </div>

    <!-- Jadwal Angsuran -->
    @if ($monthly_installment)
        <div class="mt-6 overflow-x-auto">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Jadwal Angsuran</h3>
            <table class="w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 border">Kontrak No</th>
                        <th class="p-2 border">Angsuran Ke</th>
                        <th class="p-2 border">Angsuran Per Bulan</th>
                        <th class="p-2 border">Tanggal Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedule as $item)
                        <tr class="text-center">
                            <td class="p-2 border">{{ $item['contract_no'] }}</td>
                            <td class="p-2 border">{{ $item['installment_no'] }}</td>
                            <td class="p-2 border">Rp {{ number_format($item['monthly_installment'], 2, ',', '.') }}
                            </td>
                            <td class="p-2 border">{{ $item['due_date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
