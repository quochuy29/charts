/** @type {import('tailwindcss').Config} */
export default {
  content: [
    // Laravel Blade templates
    "./resources/**/*.blade.php",
    // Vue components & JS files trong Laravel thường nằm ở resources/js
    "./resources/**/*.js",
    "./resources/**/*.vue", 
    // Giữ lại dòng này NẾU bạn vẫn để code cũ trong thư mục src (không khuyến khích với Laravel)
    // "./src/**/*.{vue,js,ts,jsx,tsx}", 
  ],
  theme: {
    extend: {
      colors: {
        border: "hsl(var(--border))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          // Lưu ý: Để hỗ trợ opacity (vd: /20), nên dùng cú pháp không có dấu phẩy hoặc dùng biến CSS
          DEFAULT: "hsl(195 85% 45%)", 
          foreground: "hsl(0 0% 100%)",
        },
      },
    },
  },
  plugins: [],
}