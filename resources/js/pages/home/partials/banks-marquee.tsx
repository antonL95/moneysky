import BankInstitutionData = App.Data.App.BankAccount.BankInstitutionData;
import { InfiniteSlider } from '@/components/ui/infinite-slider';

export default function ({ banks }: { banks: Array<{ [key: number | string]: BankInstitutionData }> }) {
    return (
        <div className={`w-full space-y-6 overflow-x-hidden`}>
            <div className="mx-auto max-w-2xl lg:text-center">
                <div className="space-y-3">
                    <h4 className="text-xl leading-none font-medium">Supported Banks</h4>
                    <p className="text-muted-foreground text-sm">
                        We support connections to over 2,000 banks across Europe.
                    </p>
                </div>
            </div>
            {banks.map((bank: { [key: number | string]: BankInstitutionData }, index: number) => {
                return (
                    <InfiniteSlider gap={24} speed={Math.max(Math.random(), 0.5) * 100} key={index}>
                        {Object.keys(bank).map(function (item: number | string) {
                            return (
                                <img
                                    src={bank[item].logo}
                                    alt={bank[item].name}
                                    className={`size-12 rounded-full`}
                                    key={bank[item].id}
                                />
                            );
                        })}
                    </InfiniteSlider>
                );
            })}
        </div>
    );
}
