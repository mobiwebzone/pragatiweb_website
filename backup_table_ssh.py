import mysql.connector
import paramiko
import time

# # SSH configuration
ssh_config = {
    'hostname': 'server340.web-hosting.com',
    'port': 21098,
    'username': 'mobizryn',
    'password': 'Mahima_1926#'
}

# MySQL configuration (remote server credentials)
mysql_config = {
    'user': 'mobizryn_admin',
    'password': 'Mahima25081996',
    'host': '127.0.0.1',  # Localhost via tunnel
    'database': 'mobizryn_mobiwebzone',
    'port': 3306  # Local forwarded port
}



def backup_table(table_name, output_file):
    # Set up SSH tunnel
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.WarningPolicy())
    
    conn = None
    try:
        # Connect to SSH
        ssh.connect(**ssh_config)
        print("SSH connection established")

        # Start tunnel
        tunnel = ssh.get_transport().open_channel(
            'direct-tcpip', ('localhost', 3306), ('127.0.0.1', 3306)
        )
        print("SSH tunnel established")

        # Connect to MySQL through tunnel
        conn = mysql.connector.connect(**mysql_config)
        cursor = conn.cursor()

        # Get table structure
        cursor.execute(f"SHOW CREATE TABLE {table_name}")
        create_table = cursor.fetchone()[1]

        # Get table data
        cursor.execute(f"SELECT * FROM {table_name}")
        rows = cursor.fetchall()

        # Write backup to file
        with open(output_file, 'w') as f:
            f.write(f"{create_table};\n\n")
            if rows:
                columns = [desc[0] for desc in cursor.description]
                insert_stmt = f"INSERT INTO {table_name} ({','.join(columns)}) VALUES "
                values = [f"({','.join(repr(val) for val in row)})" for row in rows]
                f.write(insert_stmt + ",\n".join(values) + ";\n")

        print(f"Backup of '{table_name}' saved to '{output_file}'")

    except Exception as e:
        print(f"Error: {e}")
    finally:
        if conn:
            conn.close()
        ssh.close()
        print("Connections closed")

if __name__ == "__main__":
    backup_table("Mysql_enquiries", "E:\\Mobiwebzone\\backup\\python\\mysql_enquiries_backup.sql")