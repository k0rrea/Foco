# Foco | Design System

> Guia de identidade visual, estilos e experiência do usuário para o projeto Foco.

## 1. Visão geral

**Nome:** Foco  
**Categoria:** Gerenciador de tarefas\
**Tipo:** Aplicação web responsiva  
**Tecnologias:** HTML5, CSS3, JavaScript, PHP e banco de dados relacional.

### Objetivo

Criar uma interface moderna, minimalista, intuitiva e consistente, que facilite a organização de tarefas sem distrações desnecessárias.

O design deve transmitir:

- Foco e produtividade.
- Organização e simplicidade.
- Modernidade e confiabilidade.
- Clareza visual e facilidade de navegação.

### Referências visuais

Linear, Notion e Todoist, especialmente pela organização das interfaces, hierarquia visual e uso equilibrado de cores.

Essas referências são inspirações, não modelos para reprodução literal.

---

## 2. Identidade visual

### Logotipo

A identidade visual do Foco utiliza um símbolo circular composto por:

- Um anel com uma pequena abertura na região superior direita.
- Um círculo azul centralizado.
- Tipografia geométrica, moderna e de peso elevado.

O símbolo representa concentração, direcionamento e foco.

### Versões da identidade

- **Logo horizontal escura:** símbolo preto, ponto azul e nome Foco em preto.
- **Logo horizontal clara:** símbolo branco, ponto azul e nome Foco em branco.
- **Símbolo isolado escuro:** anel preto e ponto azul.
- **Símbolo isolado claro:** anel branco e ponto azul.
- **Ícone de aplicativo:** quadrado arredondado com fundo azul-marinho, anel branco e ponto azul.

As logos horizontais e os símbolos isolados devem possuir fundo transparente.

O ícone de aplicativo deve manter seu fundo interno, com transparência somente fora dos cantos arredondados.

### Regras de utilização

1. Não distorcer as proporções.
2. Não modificar arbitrariamente as cores.
3. Não adicionar sombras ou efeitos desnecessários.
4. Preservar espaço livre ao redor do símbolo.
5. Utilizar a versão adequada ao contraste do fundo.
6. Não recriar a logo com texto ou ícones genéricos quando o arquivo original estiver disponível.

**Importante:** os caminhos e nomes dos arquivos de imagem devem ser verificados no repositório antes de serem utilizados.

---

## 3. Paleta de cores

### Cores principais

| Cor | Hexadecimal | Finalidade |
|---|---|---|
| Azul principal | `#155DFC` | Identidade, botões e destaques |
| Preto azulado | `#0B1020` | Fundo principal escuro |
| Branco | `#FFFFFF` | Superfícies claras e contraste |

O azul principal deve ser utilizado com moderação.

Evitar grandes áreas de azul saturado quando não forem necessárias.

### 3.1. Tema claro

| Elemento | Cor |
|---|---|
| Fundo principal | `#F5F7FB` |
| Sidebar e painéis | `#FFFFFF` |
| Cards e superfícies secundárias | `#F1F5F9` |
| Texto principal | `#111827` |
| Texto secundário | `#64748B` |
| Bordas | `#E2E8F0` |
| Cor principal | `#155DFC` |
| Hover principal | `#104BD0` |
| Destaque suave | `#DBEAFE` |

O tema claro deve utilizar fundos suavemente acinzentados e superfícies brancas, criando separação visual sem depender de sombras fortes.

### 3.2. Tema escuro

| Elemento | Cor |
|---|---|
| Fundo principal | `#0B1020` |
| Sidebar e painéis | `#151C2E` |
| Cards e superfícies secundárias | `#202A3D` |
| Texto principal | `#F8FAFC` |
| Texto secundário | `#94A3B8` |
| Bordas | `#30394C` |
| Cor principal | `#155DFC` |
| Hover principal | `#104BD0` |
| Destaque suave | `#1E3A8A` |

Evitar utilizar preto absoluto (`#000000`) como fundo predominante.

Priorizar tons escuros azulados, preservando a identidade visual.

### 3.3. Cores semânticas

| Estado | Tema claro | Tema escuro |
|---|---|---|
| Sucesso | `#16A34A` | `#22C55E` |
| Atenção | `#F59E0B` | `#FBBF24` |
| Erro ou atraso | `#EF4444` | `#F87171` |
| Destaque complementar | `#8B5CF6` | `#A78BFA` |

Aplicações recomendadas:

- Verde: tarefas concluídas e operações bem-sucedidas.
- Âmbar: prazos próximos e avisos.
- Vermelho: tarefas atrasadas, erros e exclusões.
- Roxo: categorias ou recursos especiais.

Nunca depender exclusivamente da cor para transmitir informações importantes.

---

## 4. Variáveis CSS

Utilizar CSS Custom Properties para centralizar as cores e facilitar a alternância entre temas.

```css
:root {
  color-scheme: light;

  --bg: #F5F7FB;
  --surface: #FFFFFF;
  --surface-secondary: #F1F5F9;

  --text: #111827;
  --text-secondary: #64748B;
  --border: #E2E8F0;

  --primary: #155DFC;
  --primary-hover: #104BD0;
  --primary-soft: #DBEAFE;

  --success: #16A34A;
  --warning: #F59E0B;
  --danger: #EF4444;
  --accent: #8B5CF6;
}

[data-theme="dark"] {
  color-scheme: dark;

  --bg: #0B1020;
  --surface: #151C2E;
  --surface-secondary: #202A3D;

  --text: #F8FAFC;
  --text-secondary: #94A3B8;
  --border: #30394C;

  --primary: #155DFC;
  --primary-hover: #104BD0;
  --primary-soft: #1E3A8A;

  --success: #22C55E;
  --warning: #FBBF24;
  --danger: #F87171;
  --accent: #A78BFA;
}
```

### Regras

- Preferir `var()` em vez de repetir cores hexadecimais.
- Não criar cores arbitrárias para componentes sem necessidade.
- Manter consistência entre os temas.
- Testar contraste, principalmente em textos pequenos e estados de hover.
- Criar novos tokens semânticos quando necessário, em vez de espalhar valores isolados.

---

## 5. Tipografia

### Fonte principal

**Inter**

Utilizar preferencialmente para textos, títulos, botões, formulários e navegação.

Fallback:

```css
font-family: "Inter", system-ui, -apple-system,
             BlinkMacSystemFont, "Segoe UI",
             sans-serif;
```

### Fonte alternativa

**Plus Jakarta Sans**

Pode ser utilizada em títulos ou elementos de destaque, caso exista uma decisão explícita de introduzir uma segunda família tipográfica.

Evitar misturar muitas fontes.

### Escala tipográfica

| Elemento | Tamanho | Peso |
|---|---|---|
| Título principal | 28–32px | 700 |
| Título de seção | 20–24px | 600 |
| Título de card | 16–18px | 600 |
| Texto padrão | 14–16px | 400 |
| Texto secundário | 13–14px | 400 |
| Labels | 12–14px | 500 |
| Botões | 14–16px | 600 |

Preferir `rem` na implementação.

Utilizar line-height adequado para leitura, geralmente entre 1.4 e 1.6 para textos corridos.

---

## 6. Componentes visuais

### Botões

**Primário**

- Fundo azul `var(--primary)`.
- Texto branco.
- Cantos suavemente arredondados.
- Hover com `var(--primary-hover)`.
- Feedback visual para foco, carregamento e estado desabilitado.

**Secundário**

- Fundo `var(--surface-secondary)`.
- Texto `var(--text)`.
- Borda discreta quando necessário.

**Destrutivo**

- Utilizar a cor semântica de erro.
- Solicitar confirmação quando a exclusão for irreversível.

### Cards

- Fundo `var(--surface)` ou `var(--surface-secondary)`, conforme a hierarquia.
- Bordas discretas.
- Border-radius sugerido: `12px`.
- Espaçamento interno entre `16px` e `24px`.
- Evitar sombras exageradas.

### Campos de formulário

- Labels visíveis.
- Bordas discretas.
- Estados de foco claramente identificáveis.
- Mensagens de erro próximas ao campo correspondente.
- Áreas clicáveis confortáveis.

### Tarefas

Uma tarefa poderá apresentar:

- Checkbox de conclusão.
- Título.
- Descrição opcional.
- Data ou prazo.
- Prioridade.
- Categoria.
- Ações de edição e exclusão.

Tarefas concluídas podem utilizar texto atenuado e tachado, sem comprometer a legibilidade.

Tarefas atrasadas devem apresentar indicadores claros.

### Indicadores de progresso

Utilizar barras ou anéis de progresso com a cor principal.

Exibir valores textuais, como porcentagens, para que a informação não dependa apenas da representação gráfica.

---

## 7. Espaçamento e geometria

Adotar uma escala consistente baseada em múltiplos de 4px.

| Token | Valor |
|---|---|
| XS | 4px |
| SM | 8px |
| MD | 12px |
| LG | 16px |
| XL | 24px |
| 2XL | 32px |
| 3XL | 48px |

### Arredondamentos

- Elementos pequenos: `6px`.
- Botões e inputs: `8px`.
- Cards: `12px`.
- Modais e painéis maiores: `16px`.
- Badges e elementos em formato de pílula: `9999px`.

Esses valores são referências iniciais e devem ser ajustados quando necessário para manter a consistência.

---

## 8. Layout e responsividade

A interface deve funcionar em computadores, tablets e smartphones.

### Desktop

- Sidebar lateral para navegação, caso faça sentido para as funcionalidades implementadas.
- Área principal com largura confortável.
- Hierarquia clara entre títulos, filtros, listas e ações.
- Evitar conteúdo excessivamente espalhado em monitores grandes.

### Tablet

- Reduzir espaçamentos quando necessário.
- Adaptar a sidebar para uma versão compacta.
- Preservar acesso às ações principais.

### Mobile

- Navegação simplificada.
- Componentes organizados verticalmente.
- Evitar rolagem horizontal.
- Garantir botões e campos com tamanho adequado ao toque.
- Priorizar as funcionalidades mais utilizadas.

Breakpoints iniciais sugeridos:

```css
/* Tablet */
@media (max-width: 1024px) {
  /* Adaptações intermediárias */
}

/* Mobile */
@media (max-width: 768px) {
  /* Layout compacto */
}

/* Telas pequenas */
@media (max-width: 480px) {
  /* Ajustes adicionais */
}
```

Os breakpoints devem ser ajustados ao comportamento real do layout, não apenas ao tipo de dispositivo.

---

## 9. Alternância entre temas

O Foco deve ser preparado para suportar modo claro e modo escuro.

A implementação poderá utilizar o atributo `data-theme` no elemento `html`.

Exemplo:

```html
<html lang="pt-BR" data-theme="dark">
```

### Comportamento esperado

1. Permitir alternância manual entre os temas.
2. Preservar a preferência do usuário, quando essa funcionalidade for implementada.
3. Considerar `prefers-color-scheme` como preferência inicial quando não existir uma escolha salva.
4. Utilizar as versões corretas das logos para cada tema.
5. Evitar flashes de tema incorreto durante o carregamento.
6. Não duplicar toda a folha de estilos para implementar os temas.

A alternância de tema é uma funcionalidade planejada, não necessariamente obrigatória para a primeira versão.

---

## 10. Acessibilidade

A interface deve seguir boas práticas de acessibilidade.

- Garantir contraste suficiente entre texto e fundo.
- Buscar conformidade com WCAG AA.
- Preservar foco visível na navegação por teclado.
- Utilizar HTML semântico.
- Associar labels aos inputs.
- Fornecer nomes acessíveis para botões que contenham somente ícones.
- Não depender apenas de cores para indicar estados.
- Respeitar `prefers-reduced-motion`.
- Garantir áreas de interação confortáveis.
- Apresentar mensagens de erro compreensíveis.

---

## 11. Animações e interações

Priorizar transições discretas e funcionais.

Duração sugerida: `150ms` a `250ms`.

Exemplo:

```css
.interactive {
  transition:
    background-color 180ms ease,
    border-color 180ms ease,
    color 180ms ease;
}

@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    scroll-behavior: auto !important;
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

Evitar animações exageradas que prejudiquem a produtividade ou distraiam o usuário.

---

## 12. Organização do CSS

Manter o código organizado e compreensível para todos os integrantes do grupo.

Sugestão de organização lógica:

1. Variáveis e tokens.
2. Reset e estilos globais.
3. Tipografia.
4. Layout.
5. Componentes.
6. Estados e utilitários.
7. Responsividade.

Essa organização pode existir em um único arquivo ou em arquivos separados, dependendo da estrutura real do projeto.

Não reorganizar a arquitetura existente sem necessidade.

### Convenções

- Utilizar nomes de classes descritivos.
- Evitar seletores excessivamente específicos.
- Evitar `!important`, salvo quando justificável.
- Não utilizar estilos inline como solução padrão.
- Reutilizar componentes e estilos sempre que possível.
- Priorizar CSS nativo antes de introduzir bibliotecas adicionais.

---

## 13. Diretrizes para o Codex

O Foco está finalizado. Este documento serve como referência de design para eventuais correções ou implementações.

Ao criar ou modificar interfaces:

1. Consultar este documento antes de tomar decisões visuais relevantes.
2. Reutilizar os tokens CSS definidos.
3. Manter consistência entre páginas e componentes.
4. Respeitar a identidade visual existente.
5. Não introduzir novas cores, fontes ou estilos sem justificativa.
6. Garantir responsividade e acessibilidade.
7. Preservar funcionalidades existentes.
8. Preservar a arquitetura em PHP tradicional, sem frameworks, API REST separada, Composer, npm ou etapa de compilação.
9. Verificar os caminhos reais das logos e outros assets antes de referenciá-los.
10. Preferir soluções simples, legíveis e adequadas a um projeto acadêmico em grupo.

### Arquitetura do projeto

O Foco utiliza uma abordagem tradicional de desenvolvimento web:

- HTML e CSS para estrutura e apresentação.
- JavaScript para interatividade.
- PHP para renderização de páginas e processamento no servidor.
- MySQL/MariaDB com PDO para persistência de dados.
- Operações CRUD para gerenciamento das informações.

Não presumir que o projeto utiliza React, Vue, Next.js ou uma arquitetura baseada principalmente em APIs.

### Prioridades

**Prioridade 1:** Funcionalidade e usabilidade.  
**Prioridade 2:** Consistência visual.  
**Prioridade 3:** Responsividade e acessibilidade.  
**Prioridade 4:** Refinamentos estéticos e animações.

O objetivo não é criar a interface mais elaborada possível, mas uma aplicação funcional, agradável e coerente.

---

## 14. Princípio central

> O design do Foco deve ajudar o usuário a se concentrar no que importa, e não competir pela sua atenção.

Simplicidade, consistência e clareza devem orientar todas as decisões visuais.

**Foco | Design System v1.0**
