export default function LogoModelo3() {
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
        Modelo 3 — Rede Neural / Minimalista
      </p>

      {/* LOGO PRINCIPAL */}
      <div style={{ display: "flex", alignItems: "center", gap: "1.1rem" }}>
        {/* Ícone de rede de nós */}
        <svg width="62" height="62" viewBox="0 0 62 62" fill="none">
          {/* Nós conectados formando um M */}
          {/* Conexões (linhas) */}
          <line x1="10" y1="48" x2="10" y2="14" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          <line x1="10" y1="14" x2="31" y2="34" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          <line x1="31" y1="34" x2="52" y2="14" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          <line x1="52" y1="14" x2="52" y2="48" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          {/* Linha horizontal NET */}
          <line x1="10" y1="48" x2="52" y2="48" stroke="rgba(255,85,0,0.3)" strokeWidth="1.5" strokeLinecap="round" strokeDasharray="3 3" />
          {/* Nós */}
          <circle cx="10" cy="14" r="5" fill="#FF5500" />
          <circle cx="31" cy="34" r="4" fill="#FF5500" opacity="0.75" />
          <circle cx="52" cy="14" r="5" fill="#FF5500" />
          <circle cx="10" cy="48" r="3.5" fill="rgba(255,85,0,0.5)" />
          <circle cx="52" cy="48" r="3.5" fill="rgba(255,85,0,0.5)" />
          {/* Ponto central brilhante */}
          <circle cx="31" cy="34" r="2" fill="white" />
        </svg>

        {/* Wordmark */}
        <div style={{ lineHeight: 1 }}>
          <div style={{ fontSize: "2.1rem", fontWeight: 900, color: "#ffffff", letterSpacing: "-0.04em", lineHeight: 1 }}>
            Moyses<span style={{
              color: "transparent",
              WebkitTextStroke: "2px #FF5500"
            }}>Net</span>
          </div>
          <div style={{ display: "flex", alignItems: "center", gap: "0.5rem", marginTop: "0.35rem" }}>
            <div style={{ flex: 1, height: "1px", background: "linear-gradient(90deg, #FF5500, transparent)" }} />
            <span style={{ fontSize: "0.55rem", fontWeight: 700, color: "rgba(255,255,255,0.4)", letterSpacing: "0.2em", textTransform: "uppercase", whiteSpace: "nowrap" }}>
              Central de Atendimento
            </span>
          </div>
        </div>
      </div>

      {/* VERSÃO COM FUNDO GRADIENTE */}
      <div style={{
        display: "flex",
        alignItems: "center",
        gap: "1.1rem",
        background: "linear-gradient(135deg, #001f3f, #002855)",
        border: "1px solid rgba(255,85,0,0.3)",
        padding: "1.25rem 2rem",
        borderRadius: "16px",
        boxShadow: "0 0 30px rgba(255,85,0,0.1)"
      }}>
        <svg width="48" height="48" viewBox="0 0 62 62" fill="none">
          <line x1="10" y1="48" x2="10" y2="14" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          <line x1="10" y1="14" x2="31" y2="34" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          <line x1="31" y1="34" x2="52" y2="14" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          <line x1="52" y1="14" x2="52" y2="48" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
          <line x1="10" y1="48" x2="52" y2="48" stroke="rgba(255,85,0,0.3)" strokeWidth="1.5" strokeLinecap="round" strokeDasharray="3 3" />
          <circle cx="10" cy="14" r="5" fill="#FF5500" />
          <circle cx="31" cy="34" r="4" fill="#FF5500" opacity="0.75" />
          <circle cx="52" cy="14" r="5" fill="#FF5500" />
          <circle cx="10" cy="48" r="3.5" fill="rgba(255,85,0,0.5)" />
          <circle cx="52" cy="48" r="3.5" fill="rgba(255,85,0,0.5)" />
          <circle cx="31" cy="34" r="2" fill="white" />
        </svg>
        <div style={{ lineHeight: 1 }}>
          <div style={{ fontSize: "1.75rem", fontWeight: 900, color: "#ffffff", letterSpacing: "-0.04em" }}>
            Moyses<span style={{ color: "transparent", WebkitTextStroke: "2px #FF5500" }}>Net</span>
          </div>
          <div style={{ display: "flex", alignItems: "center", gap: "0.4rem", marginTop: "0.3rem" }}>
            <div style={{ flex: 1, height: "1px", background: "linear-gradient(90deg, #FF5500, transparent)" }} />
            <span style={{ fontSize: "0.52rem", fontWeight: 700, color: "rgba(255,255,255,0.4)", letterSpacing: "0.18em", textTransform: "uppercase", whiteSpace: "nowrap" }}>Central de Atendimento</span>
          </div>
        </div>
      </div>

      {/* VERSÕES PEQUENAS */}
      <div style={{ display: "flex", alignItems: "center", gap: "2rem" }}>
        <div style={{ textAlign: "center" }}>
          <svg width="44" height="44" viewBox="0 0 62 62" fill="none">
            <line x1="10" y1="48" x2="10" y2="14" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
            <line x1="10" y1="14" x2="31" y2="34" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
            <line x1="31" y1="34" x2="52" y2="14" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
            <line x1="52" y1="14" x2="52" y2="48" stroke="#FF5500" strokeWidth="2.5" strokeLinecap="round" />
            <circle cx="10" cy="14" r="5" fill="#FF5500" />
            <circle cx="31" cy="34" r="4" fill="#FF5500" opacity="0.75" />
            <circle cx="52" cy="14" r="5" fill="#FF5500" />
            <circle cx="10" cy="48" r="3.5" fill="rgba(255,85,0,0.5)" />
            <circle cx="52" cy="48" r="3.5" fill="rgba(255,85,0,0.5)" />
            <circle cx="31" cy="34" r="2" fill="white" />
          </svg>
          <div style={{ fontSize: "0.6rem", color: "rgba(255,255,255,0.35)", marginTop: "0.4rem" }}>Ícone</div>
        </div>
        <div style={{ textAlign: "center" }}>
          <div style={{ fontSize: "1rem", fontWeight: 900, color: "#ffffff", letterSpacing: "-0.03em" }}>
            Moyses<span style={{ color: "transparent", WebkitTextStroke: "1.5px #FF5500" }}>Net</span>
          </div>
          <div style={{ fontSize: "0.6rem", color: "rgba(255,255,255,0.35)", marginTop: "0.2rem" }}>Versão pequena</div>
        </div>
      </div>

      <p style={{ color: "rgba(255,255,255,0.2)", fontSize: "0.68rem", textAlign: "center", maxWidth: "260px", lineHeight: 1.6 }}>
        Rede neural em M · Wordmark com outline · Linha decorativa gradiente
      </p>
    </div>
  );
}
