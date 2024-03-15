import './bootstrap';
import 'flowbite'

document.addEventListener('livewire:navigated', () => {
    initFlowbite();
});

document.addEventListener('livewire:initialized', () => {
    initFlowbite();
});
