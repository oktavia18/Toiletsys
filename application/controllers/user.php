<?php

class User extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper("html");
        $this->load->model("m_inspector");
        $this->load->library("pagination");
        $this->load->library("upload");
        $this->load->library("cetak_pdf");
    }

    public function index()
    {
        $this->load->view("partials/v_sidebar");
        $config["total_rows"] = $this->m_inspector->total_rows();
        $user = $this->m_inspector->get_limit_data();
        $this->pagination->initialize($config);
        $data = [
            "user_data" => $user,
            "total_rows" => $config["total_rows"],
        ];

        $this->load->view("v_user", $data);
        $this->load->view("partials/v_footer");
    }

    public function save()
    {
        $data = [
            "username" => $this->input->post("username"),
            "password" => md5($this->input->post("password")),
            "name" => $this->input->post("name"),
            "email" => $this->input->post("email"),
            "status" => $this->input->post("status"),
            "role" => $this->input->post("role"),
        ];
        $this->m_inspector->insert($data);
        redirect(site_url("user"));
    }

    public function update()
    {
        $data = [
            "username" => $this->input->post("username"),
            "name" => $this->input->post("name"),
            "email" => $this->input->post("email"),
            "status" => $this->input->post("status"),
            "role" => $this->input->post("role"),
        ];

        if ($this->input->post("password") != null) {
            $data["password"] = md5($this->input->post("password"));
        }
        $this->m_inspector->update($this->input->post("id"), $data);
        redirect(site_url("user"));
    }

    public function delete()
    {
        $this->m_inspector->delete($this->input->post("id"));
        redirect(site_url("user"));
    }

    public function cetak_pdf()
    {
        $pdf = new FPDF("L", "mm", "A4");
        $pdf->AddPage();
        $pdf->SetFont("Arial", "B", 16);
        $pdf->Cell(0, 7, "DATA INSPEKTUR", 0, 1, "C");
        $pdf->Cell(10, 7, "", 0, 1);
        $pdf->SetFont("Arial", "B", 10);
        $pdf->Cell(8, 6, "No", 1, 0, "C");
        $pdf->Cell(75, 6, "Username", 1, 0, "C");
        $pdf->Cell(75, 6, "Name", 1, 0, "C");
        $pdf->Cell(75, 6, "Email", 1, 0, "C");
        $pdf->Cell(25, 6, "Status", 1, 0, "C");
        $pdf->Cell(20, 6, "Role", 1, 1, "C");
        $pdf->SetFont("Arial", "", 10);
        $barang = $this->db->query("SELECT * FROM inspector")->result();
        $no = 1;
        foreach ($barang as $data) {
            $pdf->Cell(8, 6, $no, 1, 0);
            $pdf->Cell(75, 6, $data->username, 1, 0);
            $pdf->Cell(75, 6, $data->name, 1, 0);
            $pdf->Cell(75, 6, $data->email, 1, 0);
            $pdf->Cell(25, 6, $data->status == 1 ? "Aktif" : "Non-Aktif", 1, 0);
            $pdf->Cell(20, 6, $data->role == 1 ? "Admin" : "Inspektur", 1, 1);
            $no++;
        }

        $pdf->Output();
    }
}
