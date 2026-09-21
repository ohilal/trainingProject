use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username column
            $table->string('username')->unique()->after('name');

            // Add ldap_id column for LDAP integration
            $table->string('ldap_id')->nullable()->unique()->after('username');

            // Make email nullable since not all users have emails
            $table->string('email')->nullable()->change();

            // Remove unique constraint from email if it exists (handled by nullable change)
            // Drop email_verified_at as it's not needed for LDAP auth
            $table->dropColumn('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'ldap_id']);
            $table->string('email')->unique()->change();
            $table->timestamp('email_verified_at')->nullable();
        });
    }
};