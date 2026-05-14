import "./tokens.css";
import { MessageCircle, Calendar, Bot, CheckCircle, ArrowRight, ChevronRight, Zap, Shield, Clock } from "lucide-react";

export function LandingEnam() {
  return (
    <div style={{ fontFamily: "'Montserrat', sans-serif" }} className="bg-[#001f3f] text-white overflow-x-hidden">

      {/* NAVBAR */}
      <nav className="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-8 py-4"
        style={{ background: "rgba(0,18,38,0.85)", backdropFilter: "blur(12px)", borderBottom: "1px solid rgba(255,255,255,0.06)" }}>
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-lg flex items-center justify-center" style={{ background: "#FF5500" }}>
            <MessageCircle className="w-5 h-5 text-white" />
          </div>
          <span className="font-bold text-lg tracking-tight text-white">Central <span style={{ color: "#FF5500" }}>WhatsApp</span></span>
        </div>
        <div className="hidden md:flex items-center gap-8 text-sm font-semibold text-white/70">
          <a href="#" className="hover:text-white transition-colors">Início</a>
          <a href="#" className="hover:text-white transition-colors">Recursos</a>
          <a href="#" className="hover:text-white transition-colors">Planos</a>
          <a href="#" className="hover:text-white transition-colors">Contato</a>
        </div>
        <button className="px-5 py-2.5 rounded-lg text-sm font-bold text-white transition-all hover:brightness-110"
          style={{ background: "#FF5500" }}>
          Começar Agora
        </button>
      </nav>

      {/* HERO */}
      <section className="relative min-h-screen flex items-center" style={{ paddingTop: "80px" }}>
        {/* Background gradient + overlay */}
        <div className="absolute inset-0" style={{
          background: "linear-gradient(135deg, #001228 0%, #002855 60%, #001228 100%)"
        }} />
        {/* Decorative blobs */}
        <div className="absolute top-1/4 right-0 w-[600px] h-[600px] rounded-full opacity-10"
          style={{ background: "radial-gradient(circle, #FF5500 0%, transparent 70%)" }} />
        <div className="absolute bottom-0 left-1/4 w-[400px] h-[400px] rounded-full opacity-8"
          style={{ background: "radial-gradient(circle, #0066cc 0%, transparent 70%)" }} />

        <div className="relative z-10 max-w-6xl mx-auto px-8 py-24">
          {/* Badge */}
          <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-8 text-sm font-semibold"
            style={{ border: "1px solid rgba(255,85,0,0.4)", background: "rgba(255,85,0,0.08)", color: "#FF7733" }}>
            <span className="w-2 h-2 rounded-full bg-[#FF5500] inline-block" style={{ boxShadow: "0 0 6px #FF5500" }} />
            ATENDIMENTO INTELIGENTE · WHATSAPP
          </div>

          {/* Hero Title */}
          <h1 className="text-5xl md:text-7xl font-black leading-none mb-6 tracking-tight" style={{ letterSpacing: "-0.02em" }}>
            <span style={{ color: "#FF5500" }}>Agende</span> com<br />
            <span style={{ color: "#FF5500" }}>Inteligência.</span><br />
            <span className="text-white">Atenda com</span><br />
            <span style={{ color: "#FF5500" }}>Excelência.</span>
          </h1>

          {/* Subtitle */}
          <p className="text-lg md:text-xl text-white/60 max-w-xl mb-10 font-medium" style={{ lineHeight: 1.7 }}>
            Automatize agendamentos, tire dúvidas e fidelize clientes — tudo pelo WhatsApp, com IA avançada trabalhando 24h por dia.
          </p>

          {/* CTA Buttons */}
          <div className="flex flex-wrap gap-4">
            <button className="flex items-center gap-2 px-8 py-4 rounded-xl text-base font-bold text-white transition-all hover:brightness-110 hover:scale-105"
              style={{ background: "#FF5500", boxShadow: "0 8px 24px rgba(255,85,0,0.35)" }}>
              Testar Grátis <ArrowRight className="w-5 h-5" />
            </button>
            <button className="flex items-center gap-2 px-8 py-4 rounded-xl text-base font-bold text-white/80 hover:text-white transition-all"
              style={{ border: "1.5px solid rgba(255,255,255,0.2)", background: "rgba(255,255,255,0.04)" }}>
              Ver Demonstração <ChevronRight className="w-5 h-5" />
            </button>
          </div>

          {/* Stats */}
          <div className="flex gap-10 mt-14 pt-10" style={{ borderTop: "1px solid rgba(255,255,255,0.08)" }}>
            {[
              { num: "+5.000", label: "Clientes Atendidos" },
              { num: "98%", label: "Taxa de Satisfação" },
              { num: "24/7", label: "Disponibilidade" },
            ].map((s) => (
              <div key={s.label}>
                <div className="text-3xl font-black" style={{ color: "#FF5500" }}>{s.num}</div>
                <div className="text-sm text-white/50 font-semibold mt-1">{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FEATURES CARDS */}
      <section className="py-24 px-8" style={{ background: "#00172e" }}>
        <div className="max-w-6xl mx-auto">
          <div className="mb-14 text-center">
            <p className="text-sm font-bold tracking-widest mb-3" style={{ color: "#FF5500" }}>RECURSOS</p>
            <h2 className="text-4xl md:text-5xl font-black tracking-tight">
              Tudo que você precisa<br />
              <span style={{ color: "#FF5500" }}>em um só lugar</span>
            </h2>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {[
              {
                icon: <Bot className="w-7 h-7" />, title: "Chatbot com IA",
                desc: "Responde automaticamente perguntas frequentes, horários e confirmações sem intervenção humana."
              },
              {
                icon: <Calendar className="w-7 h-7" />, title: "Agendamento Inteligente",
                desc: "Clientes agendam pelo WhatsApp em segundos. Sistema verifica disponibilidade em tempo real."
              },
              {
                icon: <Zap className="w-7 h-7" />, title: "Disparos em Massa",
                desc: "Envie lembretes, promoções e confirmações para toda a sua base de clientes com um clique."
              },
              {
                icon: <Shield className="w-7 h-7" />, title: "Lista Negra",
                desc: "Controle quem pode interagir com o sistema e bloqueie contatos indesejados automaticamente."
              },
              {
                icon: <Clock className="w-7 h-7" />, title: "Histórico Completo",
                desc: "Acompanhe cada conversa, agendamento e status em um painel centralizado e intuitivo."
              },
              {
                icon: <MessageCircle className="w-7 h-7" />, title: "Multi-Atendente",
                desc: "Vários profissionais, uma só central. Cada um gerencia sua própria agenda com total autonomia."
              },
            ].map((c) => (
              <div key={c.title} className="p-7 rounded-2xl transition-all hover:translate-y-[-4px]"
                style={{ background: "rgba(255,255,255,0.03)", border: "1px solid rgba(255,255,255,0.06)" }}>
                <div className="w-12 h-12 rounded-xl flex items-center justify-center mb-5"
                  style={{ background: "rgba(255,85,0,0.12)", color: "#FF5500" }}>
                  {c.icon}
                </div>
                <h3 className="text-lg font-bold mb-2 text-white">{c.title}</h3>
                <p className="text-white/50 text-sm leading-relaxed">{c.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* PRICING */}
      <section className="py-24 px-8" style={{ background: "#001228" }}>
        <div className="max-w-6xl mx-auto">
          <div className="mb-14 text-center">
            <p className="text-sm font-bold tracking-widest mb-3" style={{ color: "#FF5500" }}>PLANOS</p>
            <h2 className="text-4xl md:text-5xl font-black tracking-tight">
              Escolha o plano<br />
              <span style={{ color: "#FF5500" }}>ideal para você</span>
            </h2>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {[
              {
                name: "Starter", price: "R$ 97", period: "/mês", featured: false,
                items: ["1 Número WhatsApp", "500 atendimentos/mês", "Agendamento básico", "Suporte por e-mail"]
              },
              {
                name: "Profissional", price: "R$ 197", period: "/mês", featured: true,
                items: ["3 Números WhatsApp", "Atendimentos ilimitados", "IA avançada", "Disparos em massa", "Suporte prioritário"]
              },
              {
                name: "Enterprise", price: "R$ 397", period: "/mês", featured: false,
                items: ["Números ilimitados", "Multi-atendente", "API completa", "Gerenciador IA", "Suporte 24/7"]
              },
            ].map((p) => (
              <div key={p.name} className={`p-8 rounded-2xl relative ${p.featured ? "scale-105" : ""}`}
                style={{
                  background: p.featured ? "linear-gradient(135deg, #FF5500, #cc3300)" : "rgba(255,255,255,0.03)",
                  border: p.featured ? "none" : "1px solid rgba(255,255,255,0.06)",
                  boxShadow: p.featured ? "0 20px 60px rgba(255,85,0,0.3)" : "none"
                }}>
                {p.featured && (
                  <div className="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-xs font-bold text-white"
                    style={{ background: "#001228" }}>
                    MAIS POPULAR
                  </div>
                )}
                <p className="text-sm font-bold text-white/60 mb-1">{p.name}</p>
                <div className="flex items-end gap-1 mb-6">
                  <span className="text-4xl font-black text-white">{p.price}</span>
                  <span className="text-white/50 mb-1">{p.period}</span>
                </div>
                <ul className="space-y-3 mb-8">
                  {p.items.map((i) => (
                    <li key={i} className="flex items-center gap-3 text-sm">
                      <CheckCircle className="w-4 h-4 flex-shrink-0" style={{ color: p.featured ? "rgba(255,255,255,0.9)" : "#FF5500" }} />
                      <span className={p.featured ? "text-white/90" : "text-white/60"}>{i}</span>
                    </li>
                  ))}
                </ul>
                <button className="w-full py-3 rounded-xl font-bold text-sm transition-all"
                  style={{
                    background: p.featured ? "rgba(255,255,255,0.15)" : "#FF5500",
                    color: "white"
                  }}>
                  Assinar agora
                </button>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FOOTER CTA */}
      <section className="py-20 px-8 text-center" style={{ background: "#001f3f" }}>
        <div className="max-w-3xl mx-auto">
          <h2 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">
            Pronto para <span style={{ color: "#FF5500" }}>transformar</span><br />
            seu atendimento?
          </h2>
          <p className="text-white/50 mb-8 text-lg">Comece grátis hoje e veja a diferença em menos de 24 horas.</p>
          <button className="px-10 py-4 rounded-xl font-bold text-base text-white inline-flex items-center gap-2"
            style={{ background: "#FF5500", boxShadow: "0 8px 24px rgba(255,85,0,0.35)" }}>
            Falar pelo WhatsApp <MessageCircle className="w-5 h-5" />
          </button>
        </div>
      </section>

    </div>
  );
}
