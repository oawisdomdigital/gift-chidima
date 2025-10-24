import { useEffect, useState } from 'react';
import { Moon, Sun } from 'lucide-react';

export function ThemeToggle() {
  const [theme, setTheme] = useState<'light' | 'dark'>('light');
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
    const storedTheme = localStorage.getItem('theme') as 'light' | 'dark' | null;

    if (storedTheme) {
      setTheme(storedTheme);
      document.documentElement.classList.toggle('dark', storedTheme === 'dark');
    } else {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const initialTheme = prefersDark ? 'dark' : 'light';
      setTheme(initialTheme);
      document.documentElement.classList.toggle('dark', prefersDark);
    }
  }, []);

  const toggleTheme = () => {
    const newTheme = theme === 'light' ? 'dark' : 'light';
    setTheme(newTheme);
    localStorage.setItem('theme', newTheme);
    document.documentElement.classList.toggle('dark', newTheme === 'dark');
  };

  if (!mounted) {
    return (
      <div className="w-10 h-10 rounded-full border border-neutral-300 dark:border-neutral-600" />
    );
  }

  return (
    <button
      onClick={toggleTheme}
      aria-label={`Switch to ${theme === 'light' ? 'dark' : 'light'} mode`}
      className="p-2 rounded-full border border-neutral-300 dark:border-neutral-600
                 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100
                 transition-all duration-300 hover:scale-105 hover:border-[#D4AF37]
                 dark:hover:border-[#D4AF37] flex items-center justify-center"
    >
      {theme === 'light' ? (
        <Moon className="w-5 h-5 transition-transform duration-300" />
      ) : (
        <Sun className="w-5 h-5 transition-transform duration-300" />
      )}
    </button>
  );
}
