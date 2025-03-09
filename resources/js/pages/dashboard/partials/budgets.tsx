import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Carousel, CarouselContent, CarouselItem } from '@/components/ui/carousel';
import { ChartConfig, ChartContainer } from '@/components/ui/chart';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import BudgetForm from '@/pages/dashboard/partials/forms/budget-form';
import { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { Trash } from 'lucide-react';
import { useState } from 'react';
import { Label, PolarGrid, PolarRadiusAxis, RadialBar, RadialBarChart } from 'recharts';
import UserBudgetData = App.Data.App.Dashboard.UserBudgetData;
import TagData = App.Data.App.Dashboard.TagData;

export default function ({
    budgets,
    tags,
    currencies,
}: {
    budgets: UserBudgetData[];
    tags: TagData[];
    currencies: { [key: string]: string };
}) {
    const { props } = usePage<SharedData>();
    const [openEdit, setOpenEdit] = useState<boolean>(false);
    const [selectedBudget, setSelectedBudget] = useState<UserBudgetData | null>(null);

    const destroy = (budget: UserBudgetData) => {
        router.delete(
            route('budget.destroy', {
                budget: budget.budgetId,
            }),
        );
    };
    const handleEditClick = (budget: UserBudgetData) => {
        setSelectedBudget(budget);
        setOpenEdit(true);
    };

    return (
        <>
            <Carousel>
                <CarouselContent className={`mx-auto justify-center`}>
                    {budgets.map((budget) => (
                        <CarouselItem className="basis-1/1 lg:basis-1/4" key={budget.id}>
                            <BudgetChart
                                budget={budget}
                                handleEditClick={(budget: UserBudgetData) => {
                                    handleEditClick(budget);
                                }}
                                handleDeleteClick={(budget: UserBudgetData) => {
                                    destroy(budget);
                                }}
                                userCurrency={props.auth.user.currency}
                            />
                        </CarouselItem>
                    ))}
                </CarouselContent>
            </Carousel>
            <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update budget</DialogTitle>
                    </DialogHeader>
                    {selectedBudget !== null && (
                        <BudgetForm
                            tags={tags}
                            budget={selectedBudget}
                            closeModal={() => setOpenEdit(false)}
                            currencies={currencies}
                        />
                    )}
                </DialogContent>
            </Dialog>
        </>
    );
}

function BudgetChart({
    budget,
    handleEditClick,
    handleDeleteClick,
    userCurrency,
}: {
    budget: UserBudgetData;
    handleEditClick: (budget: UserBudgetData) => void;
    handleDeleteClick: (budget: UserBudgetData) => void;
    userCurrency: string;
}) {
    const chartConfig = {
        balance: {
            label: budget.name,
        },
    } satisfies ChartConfig;

    const chartData = [
        {
            balance: budget.spent,
            fill: budget.spent > budget.budget ? 'hsl(var(--chart-7))' : 'hsl(var(--chart-6))',
        },
    ];

    function calculateSpendingAngle(budget: number, spent: number) {
        const spendingRatio = spent / budget;
        return spendingRatio * 360;
    }

    return (
        <Card className="flex w-full flex-col">
            <CardHeader className="items-center pb-0">
                <CardTitle>{budget.name}</CardTitle>
            </CardHeader>
            <CardContent className="flex-1 pb-0">
                <ChartContainer config={chartConfig} className="mx-auto aspect-square max-h-[250px]">
                    <RadialBarChart
                        data={chartData}
                        startAngle={0}
                        endAngle={calculateSpendingAngle(budget.budget, budget.spent)}
                        innerRadius={80}
                        outerRadius={110}
                    >
                        <PolarGrid
                            gridType="circle"
                            radialLines={false}
                            stroke="none"
                            className="first:fill-muted last:fill-background"
                            polarRadius={[86, 74]}
                        />
                        <RadialBar dataKey="balance" background cornerRadius={10} />
                        <PolarRadiusAxis tick={false} tickLine={false} axisLine={false}>
                            <Label
                                content={({ viewBox }) => {
                                    if (viewBox && 'cx' in viewBox && 'cy' in viewBox) {
                                        return (
                                            <text
                                                x={viewBox.cx}
                                                y={viewBox.cy}
                                                textAnchor="middle"
                                                dominantBaseline="middle"
                                            >
                                                <tspan
                                                    x={viewBox.cx}
                                                    y={viewBox.cy}
                                                    className="fill-foreground text-sm font-bold"
                                                >
                                                    {userCurrency}&nbsp;{budget.spent} - {budget.budget}
                                                </tspan>
                                                <tspan
                                                    x={viewBox.cx}
                                                    y={(viewBox.cy || 0) + 24}
                                                    className="fill-muted-foreground"
                                                >
                                                    spent - budget
                                                </tspan>
                                            </text>
                                        );
                                    }
                                }}
                            />
                        </PolarRadiusAxis>
                    </RadialBarChart>
                </ChartContainer>
            </CardContent>
            <CardFooter className="w-full flex-col gap-2 text-sm">
                <div className={'flex w-full justify-around'}>
                    <Button onClick={() => handleEditClick(budget)}>Edit budget</Button>
                    <Button onClick={() => handleDeleteClick(budget)} variant={'destructive'}>
                        <Trash />
                    </Button>
                </div>
            </CardFooter>
        </Card>
    );
}
