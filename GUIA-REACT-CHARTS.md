# Guia de gráficos com React no AirportControl

Este guia reúne opções comuns para criar gráficos em React e mostra como integrá-las ao AirportControl. O projeto usa Laravel 11, Blade, Vite e JavaScript, e atualmente já tem Chart.js (`chart.js`) e `chartjs-plugin-datalabels`; ainda não tem React instalado.

## Recomendação para este projeto

Para reaproveitar a biblioteca já instalada, a opção mais direta é **Chart.js com `react-chartjs-2`**. O wrapper fornece componentes React para os tipos de gráfico do Chart.js e permite migrar os gráficos atuais gradualmente. Os gráficos existentes em Blade podem continuar funcionando enquanto novos componentes React são adicionados.

“React charts” também pode significar a biblioteca específica **TanStack React Charts**. Ela é uma opção diferente das bibliotecas React para gráficos listadas abaixo; confira a documentação e a compatibilidade da versão antes de adotá-la.

## Opções de bibliotecas

| Biblioteca | Pacote comum | Quando considerar |
|---|---|---|
| Chart.js + React | `chart.js`, `react-chartjs-2` | Reutilizar Chart.js no projeto, com uma API declarativa para React. Recomendação para começar aqui. |
| Recharts | `recharts` | Gráficos comuns com componentes React composáveis e configuração simples. |
| Apache ECharts | `echarts`, `echarts-for-react` | Dashboards interativos e grande variedade de visualizações. |
| Nivo | `@nivo/core` e pacotes como `@nivo/bar` | Visualizações com componentes React e temas configuráveis. |
| Victory | `victory` | Gráficos React composáveis e customizáveis. |
| Visx | pacotes `@visx/*` | Visualizações feitas com primitivas de baixo nível, quando é necessário controlar mais detalhes. |
| TanStack React Charts | `@tanstack/react-charts` | Biblioteca chamada especificamente React Charts; avalie o estado e a documentação da versão escolhida antes de iniciar. |

Os nomes dos pacotes e os requisitos de React podem mudar. Consulte a documentação oficial da biblioteca antes de instalar ou atualizar dependências.

## Tipos de gráfico que podem ser usados

As bibliotecas variam nos tipos disponíveis, mas as visualizações mais comuns para os dados deste sistema incluem:

- **Barras/colunas**: comparar voos, passageiros ou companhias.
- **Barras horizontais**: rankings com nomes longos de aeroportos ou companhias.
- **Linhas**: acompanhar movimentação por período.
- **Pizza/rosca**: mostrar proporções, por exemplo, distribuição por tipo de voo. Evite muitas categorias.
- **Área**: visualizar tendências e volumes ao longo do tempo.
- **Dispersão**: comparar duas métricas numéricas, como voos e passageiros.
- **Combinado**: barras e linha no mesmo gráfico, por exemplo voos por companhia e uma linha de mediana.
- **Personalizado**: mapas, gauges e outras visualizações específicas, dependendo da biblioteca escolhida.

## Preparar React no Laravel + Vite

Instale React, o renderer para o navegador, o plugin React para Vite e o wrapper recomendado:

```bash
npm install react react-dom react-chartjs-2
npm install --save-dev @vitejs/plugin-react
```

Adicione o plugin React e uma entrada JSX à configuração do Vite:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/react-charts.jsx',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
});
```

Carregue a entrada React apenas nas páginas que a usam. Como o layout principal já carrega `app.js`, esta diretiva pode ser colocada em `@push('scripts')` numa view Blade que contém gráficos:

```blade
@push('scripts')
    @vite('resources/js/react-charts.jsx')
@endpush
```

O ponto de montagem deve existir na view. O layout do projeto imprime `@stack('scripts')` após o conteúdo.

## Forma 1: gráfico declarativo com `react-chartjs-2`

Crie `resources/js/components/VoosPorHorarioChart.jsx`:

```jsx
import { Bar } from 'react-chartjs-2';

export default function VoosPorHorarioChart({ labels, values }) {
    const data = {
        labels,
        datasets: [{
            label: 'Voos',
            data: values,
            backgroundColor: '#0d6efd',
            borderRadius: 6,
        }],
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { enabled: true },
        },
        scales: {
            y: { beginAtZero: true },
        },
    };

    return <Bar data={data} options={options} />;
}
```

Os componentes disponíveis incluem `Bar`, `Line`, `Pie`, `Doughnut`, `Radar`, `PolarArea`, `Bubble` e `Scatter`. Alguns tipos precisam ser importados e registrados conforme a configuração do Chart.js; o exemplo usa a configuração automática incluída pelo pacote atual `chart.js/auto`.

## Forma 2: montar um componente React numa view Blade

A view fornece os dados serializados pelo Laravel, e React monta o componente no elemento identificado por `id`.

Na view Blade:

```blade
<div id="voos-por-horario"
     data-chart='@json(["labels" => $labels, "values" => $values])'></div>

@push('scripts')
    @vite('resources/js/react-charts.jsx')
@endpush
```

Em `resources/js/react-charts.jsx`:

```jsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import VoosPorHorarioChart from './components/VoosPorHorarioChart.jsx';

const element = document.getElementById('voos-por-horario');

if (element) {
    const { labels, values } = JSON.parse(element.dataset.chart);
    createRoot(element).render(
        <div style={{ height: 320 }}>
            <VoosPorHorarioChart labels={labels} values={values} />
        </div>
    );
}
```

Para vários gráficos ou dados maiores, prefira um ponto de montagem por gráfico e valide que `labels` e `values` têm formatos compatíveis. Não concatene valores fornecidos por usuários em código JavaScript executável.

## Forma 3: gráfico alimentado por endpoint

Use esta abordagem quando o gráfico deve atualizar após a página carregar ou quando filtros alteram os resultados. Os endpoints internos de relatórios do projeto exigem autenticação.

```jsx
import { useEffect, useState } from 'react';
import { Line } from 'react-chartjs-2';

export default function MovimentacaoPorPeriodoChart({ endpoint }) {
    const [data, setData] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        const controller = new AbortController();

        fetch(endpoint, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
            signal: controller.signal,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Falha ao carregar o relatório (${response.status}).`);
                }

                return response.json();
            })
            .then(setData)
            .catch((fetchError) => {
                if (fetchError.name !== 'AbortError') {
                    setError(fetchError.message);
                }
            });

        return () => controller.abort();
    }, [endpoint]);

    if (error) return <p role="alert">{error}</p>;
    if (!data) return <p>Carregando gráfico...</p>;

    return (
        <Line
            data={{
                labels: data.labels,
                datasets: [{
                    label: 'Passageiros',
                    data: data.values,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.15)',
                    fill: true,
                    tension: 0.25,
                }],
            }}
            options={{ responsive: true, maintainAspectRatio: false }}
        />
    );
}
```

Adapte `data.labels` e `data.values` ao formato realmente retornado pelo endpoint. Trate estados de carregamento, erro e ausência de resultados; não apresente falha de rede como se fosse um gráfico vazio.

## Forma 4: combinar séries ou tipos

Chart.js permite definir o tipo em cada conjunto de dados. Um caso útil é exibir barras de voos e uma linha de referência:

```jsx
import { Chart } from 'react-chartjs-2';

const data = {
    labels: ['Companhia A', 'Companhia B', 'Companhia C'],
    datasets: [
        {
            type: 'bar',
            label: 'Voos',
            data: [120, 95, 80],
            backgroundColor: '#0d6efd',
        },
        {
            type: 'line',
            label: 'Mediana',
            data: [95, 95, 95],
            borderColor: '#198754',
            pointRadius: 0,
        },
    ],
};

export default function GraficoCombinado() {
    return <Chart type="bar" data={data} options={{ responsive: true }} />;
}
```

O componente genérico de combinação chama-se `Chart` no wrapper `react-chartjs-2`. Os componentes específicos, como `Bar` e `Line`, são mais simples para gráficos de um tipo só.

## Forma 5: Recharts

Recharts oferece uma API declarativa alternativa. Instale `recharts` no lugar de `react-chartjs-2` se decidir adotar essa biblioteca:

```bash
npm install recharts
```

Exemplo de gráfico de barras:

```jsx
import {
    Bar,
    BarChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

export default function VoosPorAeroporto({ rows }) {
    return (
        <div style={{ width: '100%', height: 320 }}>
            <ResponsiveContainer>
                <BarChart data={rows}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="aeroporto" />
                    <YAxis />
                    <Tooltip />
                    <Bar dataKey="voos" fill="#0d6efd" />
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}
```

O array esperado neste exemplo tem objetos como `{ aeroporto: 'Aeroporto Central', voos: 120 }`. Outros componentes de Recharts permitem linhas, áreas, pizza, legenda e composição de séries.

## Forma 6: Apache ECharts com React

ECharts tem muitas opções de interação e visualização. O pacote `echarts-for-react` conecta sua configuração ao React:

```bash
npm install echarts echarts-for-react
```

Exemplo de barras:

```jsx
import ReactECharts from 'echarts-for-react';

export default function VoosPorAeroporto({ labels, values }) {
    const option = {
        tooltip: { trigger: 'axis' },
        xAxis: { type: 'category', data: labels },
        yAxis: { type: 'value' },
        series: [{
            name: 'Voos',
            type: 'bar',
            data: values,
        }],
    };

    return <ReactECharts option={option} style={{ height: 320 }} />;
}
```

## Outras bibliotecas e abordagem de baixo nível

- **Nivo** fornece pacotes separados por tipo, por exemplo `@nivo/bar`, `@nivo/line` e `@nivo/pie`; consulte os exemplos da documentação para cada componente.
- **Victory** oferece componentes como `VictoryBar`, `VictoryLine` e `VictoryPie`, que podem ser combinados dentro de um container responsivo.
- **Visx** oferece primitivas de desenho e escala para compor visualizações personalizadas. É mais flexível, mas exige mais código que as opções de alto nível.
- **TanStack React Charts** é uma biblioteca própria, não um nome genérico para qualquer gráfico React. Verifique a documentação oficial, a versão publicada e a compatibilidade com a versão de React do projeto antes de escolhê-la.

Para estas opções, os conceitos são semelhantes: instalar o pacote, criar um componente React, passar dados por props e montar o componente numa página Blade através de uma entrada Vite.

## Boas práticas no AirportControl

1. Faça agregações e validações dos dados no Laravel; deixe ao frontend a apresentação e interação.
2. Use formatos consistentes: `labels` deve corresponder às categorias e cada série deve conter valores alinhados a essas categorias.
3. Defina altura para o container. Gráficos responsivos sem altura definida podem ficar invisíveis ou crescer incorretamente.
4. Apresente unidades, legenda, tooltips e estados vazios/erro; não dependa apenas de cores para transmitir significado.
5. Limite categorias em gráficos de pizza/rosca e prefira barras para rankings com muitas categorias.
6. Para dados monetários ou contagens grandes, formate os valores em tooltip e eixos.
7. Evite migrar todos os gráficos numa única alteração. Adicione React a uma view primeiro e mantenha o `AirportCharts`/Chart.js atual funcionando durante a transição.
8. Não instale várias bibliotecas para resolver o mesmo caso sem uma necessidade específica; cada dependência aumenta o bundle e a manutenção.

## Referências oficiais

- [Chart.js](https://www.chartjs.org/docs/latest/)
- [react-chartjs-2](https://react-chartjs-2.js.org/)
- [Recharts](https://recharts.org/)
- [Apache ECharts](https://echarts.apache.org/)
- [Nivo](https://nivo.rocks/)
- [Victory](https://commerce.nearform.com/open-source/victory/)
- [Visx](https://airbnb.io/visx/)
- [TanStack React Charts](https://tanstack.com/charts)
