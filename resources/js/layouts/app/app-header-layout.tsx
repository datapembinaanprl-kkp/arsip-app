import { ReactNode } from "react";
import { AppContent } from "@/components/app-content";
import { AppHeader } from "@/components/app-header";
import { AppShell } from "@/components/app-shell";
import type { AppLayoutProps } from "@/types";

interface Props extends AppLayoutProps {
    children: ReactNode;
}

export default function AppHeaderLayout({
    children,
    breadcrumbs,
}: Props) {
    return (
        <AppShell>
            {/* HEADER */}
            <AppHeader breadcrumbs={breadcrumbs} />

            {/* CONTENT */}
            <AppContent>
                {children}
            </AppContent>
        </AppShell>
    );
}