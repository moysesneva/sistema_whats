export default function LogoModelo2() {
  return (
    <div style={{
      minHeight: "100vh",
      background: "#001228",
      display: "flex",
      flexDirection: "column",
      alignItems: "center",
      justifyContent: "center",
      fontFamily: "'Montserrat', sans-serif",
      gap: "3rem",
      padding: "2rem"
    }}>
      <p style={{ color: "rgba(255,255,255,0.35)", fontSize: "0.72rem", fontWeight: 700, letterSpacing: "0.2em", textTransform: "uppercase" }}>
        Modelo 2 — Chatbot & WhatsApp
      </p>

      {/* LOGO PRINCIPAL */}
      <div style={{ display: "flex", alignItems: "center", gap: "1rem" }}>
        {/* Ícone balão de chat com raio */}
        <svg width="68" height="68" viewBox="0 0 68 68" fill="none">
          {/* Círculo de fundo */}
          <circle cx="34" cy="34" r="32" fill="#FF5500" />
          {/* Balão de chat */}
          <path
            d="M14 20 Q14 14 20 14 L48 14 Q54 14 54 20 L54 40 Q54 46 48 46 L38 46 L28 56 L28 46 L20 46 Q14 46 14 40 Z"
            fill="white"
            opacity="0.95"
          />
          {/* Raio / lightning dentro do balão */}
          <path
            d="M37 22 L28 34 L34 34 L31 46 L43 30 L37 30 Z"
            fill="#FF5500"
          />
        </svg>

        {/* Wordmark */}
        <div style={{ lineHeight: 1.1 }}>
          <div style={{ display: "flex", alignItems: "baseline" }}>
            <span style={{ fontSize: "2rem", fontWeight: 900, color: "#ffffff", letterSpacing: "-0.03em" }}>MOYSES</span>
            <span style={{
              fontSize: "2rem",
              fontWeight: 900,
              background: "linear-gradient(135deg, #FF5500, #FF8833)",
              WebkitBackgroundClip: "text",
              WebkitTextFillColor: "transparent",
              letterSpacing: "-0.03em"
            }}>NET</span>
          </div>
          <div style={{
            display: "flex",
            alignItems: "center",
            gap: "0.4rem",
            marginTop: "0.25rem"
          }}>
            <div style={{ width: "20px", height: "1.5px", background: "#FF5500" }} />
            <span style={{ fontSize: "0.58rem", fontWeight: 700, color: "rgba(255,255,255,0.45)", letterSpacing: "0.18em", textTransform: "uppercase" }}>
              IA · WhatsApp · Agendamento
            </span>
            <div style={{ width: "20px", height: "1.5px", background: "#FF5500" }} />
          </div>
        </div>
      </div>

      {/* VERSÃO FUNDO BRANCO */}
      <div style={{
        display: "flex",
        alignItems: "center",
        gap: "1rem",
        background: "#ffffff",
        padding: "1.25rem 2rem",
        borderRadius: "16px"
      }}>
        <svg width="52" height="52" viewBox="0 0 68 68" fill="none">
          <circle cx="34" cy="34" r="32" fill="#FF5500" />
          <path d="M14 20 Q14 14 20 14 L48 14 Q54 14 54 20 L54 40 Q54 46 48 46 L38 46 L28 56 L28 46 L20 46 Q14 46 14 40 Z" fill="white" opacity="0.95" />
          <path d="M37 22 L28 34 L34 34 L31 46 L43 30 L37 30 Z" fill="#FF5500" />
        </svg>
        <div style={{ lineHeight: 1.1 }}>
          <div style={{ display: "flex", alignItems: "baseline" }}>
            <span style={{ fontSize: "1.75rem", fontWeight: 900, color: "#001228", letterSpacing: "-0.03em" }}>MOYSES</span>
            <span style={{ fontSize: "1.75rem", fontWeight: 900, color: "#FF5500", letterSpacing: "-0.03em" }}>NET</span>
          </div>
          <div style={{ fontSize: "0.55rem", fontWeight: 700, color: "rgba(0,18,40,0.4)", letterSpacing: "0.15em", textTransform: "uppercase", marginTop: "0.2rem" }}>
            IA · WhatsApp · Agendamento
          </div>
        </div>
      </div>

      {/* VERSÕES PEQUENAS */}
      <div style={{ display: "flex", alignItems: "center", gap: "2rem" }}>
        <div style={{ textAlign: "center" }}>
          <svg width="44" height="44" viewBox="0 0 68 68" fill="none">
            <circle cx="34" cy="34" r="32" fill="#FF5500" />
            <path d="M14 20 Q14 14 20 14 L48 14 Q54 14 54 20 L54 40 Q54 46 48 46 L38 46 L28 56 L28 46 L20 46 Q14 46 14 40 Z" fill="white" opacity="0.95" />
            <path d="M37 22 L28 34 L34 34 L31 46 L43 30 L37 30 Z" fill="#FF5500" />
          </svg>
          <div style={{ fontSize: "0.6rem", color: "rgba(255,255,255,0.35)", marginTop: "0.4rem" }}>Ícone</div>
        </div>
        <div style={{ textAlign: "center" }}>
          <div style={{ display: "flex", alignItems: "baseline" }}>
            <span style={{ fontSize: "1rem", fontWeight: 900, color: "#ffffff" }}>MOYSES</span>
            <span style={{ fontSize: "1rem", fontWeight: 900, color: "#FF5500" }}>NET</span>
          </div>
          <div style={{ fontSize: "0.6rem", color: "rgba(255,255,255,0.35)", marginTop: "0.2rem" }}>Versão pequena</div>
        </div>
      </div>

      <p style={{ color: "rgba(255,255,255,0.2)", fontSize: "0.68rem", textAlign: "center", maxWidth: "260px", lineHeight: 1.6 }}>
        Balão de chat com raio IA · Wordmark uppercase · Linhas decorativas
      </p>
    </div>
  );
}
