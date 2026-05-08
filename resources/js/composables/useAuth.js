import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useAuth() {
    const page = usePage()

    const user        = computed(() => page.props.auth?.user ?? null)
    const role        = computed(() => user.value?.role ?? null)
    const permissions = computed(() => user.value?.permissions ?? [])
    const timKerja    = computed(() => user.value?.tim_kerja ?? null)

    const hasRole = (...roles) =>
        roles.includes(role.value)

    const can = (permission) =>
        permissions.value.includes(permission)

    const canAny = (...perms) =>
        perms.some(p => permissions.value.includes(p))

    return { user, role, timKerja, hasRole, can, canAny }
}
