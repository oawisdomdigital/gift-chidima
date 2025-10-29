import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { Calendar, Sparkles } from "lucide-react";
import { Link } from "react-router-dom";
import { Button } from "./ui/button";
import { apiUrl } from "../lib/config";

export function FinalCTA() {
  const [cta, setCta] = useState<any>(null);

  useEffect(() => {
  fetch(apiUrl('get_final_cta.php'))
      .then((res) => res.json())
      .then((data) => setCta(data))
      .catch((err) => console.error("Failed to fetch CTA:", err));
  }, []);

  if (!cta) return null; // show nothing until data is loaded

  return (
    <section className="relative py-24 md:py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
      <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjAzKSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-20"></div>

      <div className="absolute top-20 left-10 w-64 h-64 bg-amber-500 rounded-full opacity-10 blur-3xl"></div>
      <div className="absolute bottom-20 right-10 w-80 h-80 bg-blue-500 rounded-full opacity-10 blur-3xl"></div>

      <div className="relative z-10 container mx-auto px-6 md:px-8 text-center">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="max-w-4xl mx-auto"
        >
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5 }}
            className="inline-block mb-6"
          >
            <div className="p-4 bg-amber-500/20 rounded-full">
              <Sparkles className="w-10 h-10 text-amber-400" />
            </div>
          </motion.div>

          <h2 className="font-['Playfair_Display'] text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
            {cta.title}
          </h2>

          <p className="text-xl md:text-2xl text-slate-300 mb-12 leading-relaxed max-w-3xl mx-auto">
            {cta.description}
          </p>

          <div className="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <Link to={cta.button1_link}>
              <Button
                size="lg"
                className="btn-gold font-semibold px-10 py-7 text-lg rounded-2xl shadow-2xl transition-all duration-300 group border-2"
              >
                <Calendar className="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" />
                {cta.button1_text}
              </Button>
            </Link>

            <Link to={cta.button2_link}>
              <Button
                size="lg"
                className="bg-transparent border-2 border-white text-[#D4AF37] hover:bg-white hover:text-black font-semibold px-10 py-7 text-lg rounded-2xl transition-all duration-300"
              >
                {cta.button2_text}
              </Button>
            </Link>
          </div>

          <p className="text-slate-400 mt-8 text-sm">
            Join thousands of leaders who have experienced transformation through Dr. Gift's guidance
          </p>
        </motion.div>
      </div>
    </section>
  );
}
