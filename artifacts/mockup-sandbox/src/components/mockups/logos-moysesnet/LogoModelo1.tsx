export default function LogoModelo1() {
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
        Modelo 1 — Monograma Geométrico
      </p>

      {/* LOGO GRANDE */}
      <div style={{ display: "flex", alignItems: "center", gap: "1.1rem" }}>
        {/* Ícone M geométrico */}
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
          {/* Hexágono de fundo */}
          <polygon
            points="32,2 60,17 60,47 32,62 4,47 4,17"
            fill="#FF5500"
          />
          {/* Letra M dentro */}
          <path
            d="M16 44 L16 20 L32 36 L48 20 L48 44"
            stroke="white"
            strokeWidth="4.5"
            strokeLinecap="round"
            strokeLinejoin="round"
            fill="none"
          />
        </svg>

        {/* Wordmark */}
        <div style={{ lineHeight: 1 }}>
          <div style={{ display: "flex", alignItems: "baseline", gap: "0" }}>
            <span style={{
              fontSize: "2rem",
              fontWeight: 900,
              color: "#ffffff",
              letterSpacing: "-0.03em"
            }}>Moyses</span>
            <span style={{
              fontSize: "2rem",
              fontWeight: 900,
              color: "#FF5500",
              letterSpacing: "-0.03em"
            }}>Net</span>
          </div>
          <div style={{
            fontSize: "0.58rem",
            fontWeight: 600,
            color: "rgba(255,255,255,0.4)",
            letterSpacing: "0.22em",
            textTransform: "uppercase",
            marginTop: "0.2rem"
          }}>
            Atendimento Inteligente
          </div>
        </div>
      </div>

      {/* VERSÃO EM FUNDO LARANJA */}
      <div style={{
        display: "flex",
        alignItems: "center",
        gap: "1.1rem",
        background: "#FF5500",
        padding: "1.25rem 2rem",
        borderRadius: "16px"
      }}>
        <svg width="48" height="48" viewBox="0 0 64 64" fill="none">
          <polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="rgba(0,0,0,0.2)" />
          <path d="M16 44 L16 20 L32 36 L48 20 L48 44" stroke="white" strokeWidth="4.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
        </svg>
        <div style={{ lineHeight: 1 }}>
          <div style={{ display: "flex", alignItems: "baseline" }}>
            <span style={{ fontSize: "1.75rem", fontWeight: 900, color: "#ffffff", letterSpacing: "-0.03em" }}>Moyses</span>
            <span style={{ fontSize: "1.75rem", fontWeight: 900, color: "#001228", letterSpacing: "-0.03em" }}>Net</span>
          </div>
          <div style={{ fontSize: "0.55rem", fontWeight: 600, color: "rgba(255,255,255,0.7)", letterSpacing: "0.2em", textTransform: "uppercase", marginTop: "0.2rem" }}>
            Atendimento Inteligente
          </div>
        </div>
      </div>

      {/* VERSÃO PEQUENA / EMBLEMA */}
      <div style={{ display: "flex", alignItems: "center", gap: "1.5rem" }}>
        <div style={{ textAlign: "center" }}>
          <svg width="48" height="48" viewBox="0 0 64 64" fill="none">
            <polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#FF5500" />
            <path d="M16 44 L16 20 L32 36 L48 20 L48 44" stroke="white" strokeWidth="4.5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
          </svg>
          <div style={{ fontSize: "0.6rem", color: "rgba(255,255,255,0.35)", marginTop: "0.4rem" }}>Ícone</div>
        </div>
        <div style={{ textAlign: "center" }}>
          <div style={{ display: "flex", alignItems: "baseline" }}>
            <span style={{ fontSize: "1.1rem", fontWeight: 900, color: "#ffffff", letterSpacing: "-0.02em" }}>Moyses</span>
            <span style={{ fontSize: "1.1rem", fontWeight: 900, color: "#FF5500", letterSpacing: "-0.02em" }}>Net</span>
          </div>
          <div style={{ fontSize: "0.6rem", color: "rgba(255,255,255,0.35)", marginTop: "0.2rem" }}>Versão pequena</div>
        </div>
      </div>

      <p style={{ color: "rgba(255,255,255,0.2)", fontSize: "0.68rem", textAlign: "center", maxWidth: "260px", lineHeight: 1.6 }}>
        Hexágono com letra M · Wordmark bicolor · Subtítulo técnico
      </p>
    </div>
  );
}
