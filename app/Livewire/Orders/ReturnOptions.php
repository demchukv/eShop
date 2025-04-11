<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\OrderItems;

class ReturnOptions extends Component
{
    public $orderItemId;
    public $customCourierOptions = [];
    public $selectedOption;

    public function mount($orderItemId)
    {
        $this->orderItemId = $orderItemId;

        // Приклад: отримання опцій для повернення (можете адаптувати під вашу логіку)
        $orderItem = OrderItems::find($this->orderItemId);
        if ($orderItem && $orderItem->order->is_custom_courier == 1) {
            $this->customCourierOptions = [
                'option1' => 'Courier Service A',
                'option2' => 'Courier Service B',
                'option3' => 'Self Pickup',
            ];
        } else {
            abort(404, 'Order item not found or custom courier not applicable');
        }
    }

    public function saveReturnOption()
    {
        // Логіка збереження вибраної опції повернення
        $this->validate([
            'selectedOption' => 'required|in:' . implode(',', array_keys($this->customCourierOptions)),
        ]);

        // Приклад: оновлення статусу або збереження опції
        $orderItem = OrderItems::find($this->orderItemId);
        if ($orderItem) {
            // Додайте вашу логіку збереження, наприклад:
            $orderItem->update(['return_option' => $this->selectedOption]);
            session()->flash('message', 'Return option saved successfully!');
        }

        return redirect()->route('orders.details', $orderItem->order_id);
    }

    public function render()
    {
        return view('livewire.elegant.orders.return-options');
    }
}
