import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartConfig, ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart';
import { Area, AreaChart, CartesianGrid, Label, Pie, PieChart, XAxis } from 'recharts';

export default function ({
    totalAssets,
    assets,
    historicalAssets,
}: {
    totalAssets: App.Data.App.Dashboard.AssetData;
    assets: App.Data.App.Dashboard.AssetData[];
    historicalAssets: App.Data.App.Dashboard.HistoricalAssetsData[];
}) {
    return (
        <>
            <PieChartComponent totalAssets={totalAssets} assets={assets} />
            <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                {historicalAssets &&
                    historicalAssets.map((asset, index) => <AreaChartComponent historicalAsset={asset} key={index} />)}
            </div>
        </>
    );
}

function PieChartComponent({
    totalAssets,
    assets,
}: {
    totalAssets: App.Data.App.Dashboard.AssetData;
    assets: App.Data.App.Dashboard.AssetData[];
}) {
    const chartData = assets.map((asset: App.Data.App.Dashboard.AssetData) => {
        return {
            title: asset.assetName,
            value: asset.balanceNumeric,
            fill: asset.color,
        };
    });

    const chartConfig = {
        total: {
            label: 'Total Assets',
        },
    } satisfies ChartConfig;

    return (
        <Card className="mx-auto flex min-w-full flex-col md:h-[450px] lg:min-w-1/3">
            <CardHeader className="items-center pb-0">
                <CardTitle>Assets breakdown</CardTitle>
                <CardDescription className={`block md:hidden`}>Total: {totalAssets.balance}</CardDescription>
            </CardHeader>
            <CardContent className="flex-1 pb-0">
                <ChartContainer config={chartConfig} className="mx-auto aspect-square max-h-[300px] md:max-h-[400px]">
                    <PieChart>
                        <ChartTooltip cursor={false} content={<ChartTooltipContent hideLabel />} />
                        <Pie
                            className={`block md:hidden`}
                            data={chartData}
                            dataKey="value"
                            nameKey="title"
                            innerRadius={60}
                        />
                        <Pie
                            className={`hidden md:block`}
                            data={chartData}
                            dataKey="value"
                            nameKey="title"
                            innerRadius={140}
                        >
                            <Label
                                content={({ viewBox }) => {
                                    if (viewBox && 'cx' in viewBox && 'cy' in viewBox) {
                                        return (
                                            <text
                                                className={`hidden md:block`}
                                                x={viewBox.cx}
                                                y={viewBox.cy}
                                                textAnchor="middle"
                                                dominantBaseline="middle"
                                            >
                                                <tspan
                                                    x={viewBox.cx}
                                                    y={viewBox.cy}
                                                    className="fill-foreground text-3xl font-bold"
                                                >
                                                    {totalAssets.balance}
                                                </tspan>
                                                <tspan
                                                    x={viewBox.cx}
                                                    y={(viewBox.cy || 0) + 24}
                                                    className="fill-muted-foreground"
                                                >
                                                    Total
                                                </tspan>
                                            </text>
                                        );
                                    }
                                }}
                            />
                        </Pie>
                    </PieChart>
                </ChartContainer>
            </CardContent>
        </Card>
    );
}

function AreaChartComponent({ historicalAsset }: { historicalAsset: App.Data.App.Dashboard.HistoricalAssetsData }) {
    const chartConfig = {
        balance: {
            label: historicalAsset.assetName,
            color: historicalAsset.color,
        },
    } satisfies ChartConfig;

    const chartData = historicalAsset.assetsData.map((asset: App.Data.App.Dashboard.HistoricalAssetData) => {
        return {
            date: asset.date,
            balance: asset.balanceNumeric,
        };
    });

    return (
        <Card>
            <CardHeader>
                <CardTitle>{historicalAsset.assetName}</CardTitle>
            </CardHeader>
            <CardContent>
                <ChartContainer config={chartConfig}>
                    <AreaChart accessibilityLayer data={chartData}>
                        <CartesianGrid vertical={false} />
                        <XAxis dataKey="date" tickLine={false} axisLine={false} />
                        <ChartTooltip cursor={false} content={<ChartTooltipContent indicator="line" />} />
                        <Area
                            dataKey="balance"
                            type="natural"
                            fill="var(--color-balance)"
                            fillOpacity={0.4}
                            stroke="var(--color-balance)"
                        />
                    </AreaChart>
                </ChartContainer>
            </CardContent>
        </Card>
    );
}
