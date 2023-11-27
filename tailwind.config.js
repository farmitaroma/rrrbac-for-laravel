/** @type {import('tailwindcss').Config} */
import preset from './vendor/filament/support/tailwind.config.preset'

module.exports = {
  presets: [preset],
  content: [
    "./resources/views/**/*.blade.php",
    './src/Livewire/**/*.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}