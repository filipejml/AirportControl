# Guia dos relatórios do AirportControl

Este documento descreve como os relatórios estão organizados no AirportControl, quais bibliotecas e componentes os produzem e como os dados chegam às páginas.

## Visão geral

Os relatórios são implementados no backend Laravel e apresentados em views Blade. Algumas páginas também desenham gráficos no navegador usando Chart.js.

O fluxo típico é:

1. O usuário abre uma página de relatório Blade.
2. A view carrega filtros e/ou consulta um endpoint interno autenticado.
3. O Laravel valida filtros, consulta os modelos e agrega os dados.
4. O controller ou um serviço de relatório retorna dados JSON ou a própria view.
5. A página mostra os dados em tabela, cards ou gráficos.
6. Relatórios de voos podem ser exportados separadamente para CSV ou PDF.

## Bibliotecas e tecnologias

| Parte | Tecnologia usada | Função |
|---|---|---|
| Backend | Laravel e Eloquent ORM | Rotas, validação, acesso ao banco e processamento dos dados. |
| Páginas | Blade | Renderizar páginas e templates dos relatórios. |
| Gráficos | Chart.js (`chart.js/auto`) | Desenhar gráficos no navegador. |
| Rótulos dos gráficos | `chartjs-plugin-datalabels` | Exibir rótulos de dados quando habilitados. |
| Helper de gráficos | `AirportCharts` em `resources/js/charts/chart-factory.js` | Padronizar cores, opções e criação/destruição dos gráficos Chart.js. |
| PDF | `barryvdh/laravel-dompdf` | Gerar PDFs a partir de views Blade/HTML. |
| CSV | funções nativas do PHP (`fputcsv`) | Gerar e transmitir a exportação CSV de voos. |
| Cache de respostas JSON | Middleware próprio `CacheRelatorioResponse` | Guardar por um minuto respostas JSON bem-sucedidas dos endpoints de relatórios. |

O projeto **não usa React** na implementação atual dos relatórios. Não há dependência React no `package.json`; os gráficos existentes usam Chart.js com JavaScript nas views Blade.

## Relatórios disponíveis

Os endpoints internos de dados estão no grupo `/api/relatorios` e são implementados por `RelatorioController`:

| Relatório | Endpoint | Service |
|---|---|---|
| Companhias por aeroporto | `GET /api/relatorios/companhias-por-aeroporto` | Consulta e agregação no controller. |
| Voos por aeroporto | `GET /api/relatorios/voos-por-aeroporto` | Consulta e agregação no controller. |
| Desempenho de companhias | `GET /api/relatorios/desempenho-companhias` | `DesempenhoCompanhiasService` |
| Movimentação por período | `GET /api/relatorios/movimentacao-por-periodo` | `MovimentacaoPorPeriodoService` |
| Ranking de aeroportos | `GET /api/relatorios/ranking-aeroportos` | `RankingAeroportosService` |
| Ocupação de voos | `GET /api/relatorios/ocupacao-voos` | `OcupacaoVoosService` |

As páginas Blade têm versões para usuário comum e administrador. A navegação e a visibilidade são controladas pelas rotas autenticadas e pelos registros de relatórios habilitados para usuários.

## Autenticação, proteção e cache

Os endpoints de dados do relatório são destinados ao frontend autenticado, não a uma API pública. Eles passam pelos middlewares `web`, `auth`, `throttle:internal-api` e `cache.relatorios`.

- É necessário estar autenticado na aplicação.
- O rate limit evita chamadas excessivas.
- O cache usa a URL completa, incluindo query string, como parte da chave.
- Apenas respostas JSON bem-sucedidas são armazenadas.
- O tempo de cache é de um minuto.
- A resposta inclui o header `X-Report-Cache` com `HIT` ou `MISS`.

Os endpoints usam `RelatorioFiltrosRequest` para validar os parâmetros permitidos.

## Filtros aceitos

Filtros gerais:

| Parâmetro | Valores/formato |
|---|---|
| `periodo` | `hoje`, `semana`, `mes` ou `ano` |
| `aeroporto_id` | ID inteiro existente em `aeroportos` |
| `companhia_id` | ID inteiro existente em `companhias_aereas` |
| `aeronave_id` | ID inteiro existente em `aeronaves` |

Filtros específicos:

| Relatório | Parâmetro | Valores/formato |
|---|---|---|
| Movimentação por período | `agrupamento` | `dia`, `semana`, `mes` ou `ano` |
| Movimentação por período | `data_inicio`, `data_fim` | Datas; `data_fim` deve ser igual ou posterior a `data_inicio`. |
| Ranking de aeroportos | `ordenacao` | `total_voos`, `total_passageiros`, `media_passageiros_por_voo`, `total_companhias` ou `media_geral` |
| Ocupação de voos | `faixa` | `baixa`, `media`, `alta` ou `lotado` |

Exemplo de chamada filtrada:

```text
/api/relatorios/movimentacao-por-periodo?periodo=ano&agrupamento=mes&data_inicio=2026-01-01&data_fim=2026-12-31&aeroporto_id=2
```

## Formato da resposta JSON

Os endpoints que usam o helper `respostaApiRelatorio` seguem este envelope:

```json
{
  "success": true,
  "data": [],
  "meta": {
    "filters": {},
    "timestamp": "2026-10-05T12:00:00+00:00"
  }
}
```

O conteúdo de `data` varia por endpoint. Por exemplo, `MovimentacaoPorPeriodoService` retorna a série agrupada em períodos e totais:

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "chave": "2026-01",
        "label": "01/2026",
        "total_voos": 120,
        "total_passageiros": 25000,
        "media_passageiros_por_voo": 208,
        "variacao_percentual": null
      }
    ],
    "totais": {
      "total_periodos": 1,
      "total_voos": 120,
      "total_passageiros": 25000
    }
  },
  "meta": {
    "filters": { "agrupamento": "mes" },
    "timestamp": "2026-10-05T12:00:00+00:00"
  }
}
```

Os números acima são ilustrativos. Consulte o serviço/controller correspondente para confirmar os campos específicos consumidos por cada view.

## Exemplo de consumo no frontend

As páginas Blade atuais seguem seus próprios scripts. Uma chamada JavaScript pode consumir um endpoint autenticado da mesma origem assim:

```js
async function carregarRelatorio(endpoint) {
    const response = await fetch(endpoint, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error(`Falha ao carregar o relatório (${response.status}).`);
    }

    const resultado = await response.json();

    if (!resultado.success) {
        throw new Error('O servidor não conseguiu gerar o relatório.');
    }

    return resultado.data;
}
```

Trate erros HTTP, validação e ausência de dados de forma explícita. Os dados agregados devem vir do backend; o frontend deve se concentrar em exibir tabelas, cards e gráficos.

## Exibição em gráficos

Os gráficos de páginas Blade usam a função `AirportCharts.create()` definida em `resources/js/charts/chart-factory.js`. O helper oferece configurações para Chart.js e evita manter mais de uma instância associada ao mesmo canvas.

Exemplo reduzido:

```js
AirportCharts.bar(
    document.getElementById('voosPorAeroportoChart'),
    {
        labels: ['Aeroporto Central', 'Aeroporto Norte'],
        datasets: [{
            label: 'Voos',
            data: [120, 85],
            backgroundColor: ['#0d6efd', '#198754'],
        }],
    },
    { responsive: true }
);
```

Também há métodos auxiliares para gráficos de linha, pizza e rosca: `AirportCharts.line`, `AirportCharts.pie` e `AirportCharts.doughnut`. Para mais detalhes e exemplos, consulte [GUIA-REACT-CHARTS.md](./GUIA-REACT-CHARTS.md); apesar do nome, o guia também explica Chart.js e compara opções futuras para React.

## Exportações de voos

As exportações de voos não são geradas pelos endpoints JSON de relatórios:

- **CSV:** a rota autenticada `voos.export.csv` usa `VooController::exportCSV`, transmite o arquivo e escreve linhas com `fputcsv`, separadas por ponto e vírgula.
- **PDF:** a rota `voos.export.pdf` usa `VooController::exportPDF`, carrega a view `pdf.voos-relatorio` com `Pdf::loadView()` e define papel A4 em orientação paisagem.
- **PDF por companhia:** a rota `companhias.voos.pdf` usa `CompanhiaAereaController::exportVoosPdf` e uma view PDF própria da companhia.

Esses recursos usam os filtros definidos em suas respectivas ações. Não se deve assumir que aceitam automaticamente todos os filtros dos endpoints `/api/relatorios`.

## Locais principais no código

- Rotas internas e middleware dos endpoints: `routes/api.php`
- Rotas web e de exportação: `routes/web.php`
- Lógica principal dos relatórios: `app/Http/Controllers/RelatorioController.php`
- Validação dos filtros: `app/Http/Requests/RelatorioFiltrosRequest.php`
- Serviços de agregação: `app/Services/Relatorios/`
- Middleware de cache: `app/Http/Middleware/CacheRelatorioResponse.php`
- Fábrica/helper Chart.js: `resources/js/charts/chart-factory.js`
- Views dos relatórios: `resources/views/relatorios/` e `resources/views/admin/relatorios/`
- Templates PDF: `resources/views/pdf/`
- Dependências PHP e JavaScript: `composer.json` e `package.json`

## Como adicionar um novo relatório

1. Defina as métricas e filtros necessários e valide-os num `FormRequest`.
2. Coloque a agregação reutilizável num service em `app/Services/Relatorios/` quando apropriado.
3. Adicione a ação correspondente ao `RelatorioController`.
4. Registre o endpoint sob o grupo autenticado e protegido por cache/rate limit, se o relatório for consumido via JSON.
5. Defina e documente o formato estável da resposta (`data` e `meta`).
6. Crie ou atualize as views Blade, estados vazios e mensagens de erro.
7. Se incluir gráficos, use o helper `AirportCharts` existente; para PDF ou CSV, mantenha cada formato em sua ação de exportação apropriada.
8. Acrescente testes para filtros, agregações, autorização e formato da resposta.
